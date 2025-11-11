<?php

namespace App\Services;

use App\Enums\CartStatus;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Product;

use DB;
use Illuminate\Validation\ValidationException;

class EcommerceService
{
    /**
     * Enfocado al usuario customer
     * Devolver catalogo de productos con precios y descuentos
     * Crear carritos con varios productos
     * Reducir inventario
     */
    protected $cartService;
    protected $cartItemService;
    protected $productService;
    protected $orderService;
    protected $orderItemService;
    protected $priceService;
    public function __construct(
        CartService $cartService,
        CartItemService $cartItemService,
        ProductService $productService,
        OrderService $orderService,
        PriceService $priceService,
        OrderItemService $orderItemService
    ) {
        $this->cartService = $cartService;
        $this->cartItemService = $cartItemService;
        $this->productService = $productService;
        $this->orderService = $orderService;
        $this->priceService = $priceService;
        $this->orderItemService = $orderItemService;
    }

public function checkoutCart(int $cartId): Cart
{
    return DB::transaction(function () use ($cartId) {
        $now = now();

        /** @var Cart $cart */
        $cart = Cart::query()
            ->lockForUpdate()
            ->with(['items.price.product', 'items.price.discount'])
            ->findOrFail($cartId);

        // 0) Validaciones de estado y contenido
        if ($cart->status !== 'active') {
            throw ValidationException::withMessages([
                'status' => "El carrito {$cart->id} no está en estado 'activo'.",
            ]);
        }
        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'No puedes hacer checkout de un carrito vacío.',
            ]);
        }

        // 1) Validar vendibilidad y acumular unidades a descontar por product_id
        $unitsByProduct = []; // product_id => total_units
        foreach ($cart->items as $item) {
            $qtyRequested = (int)($item->quantity ?? 1);

            /** @var Price $price */
            $price = Price::query()
                ->sellable() // debe existir tu scope; si no, filtra por status y discount activo
                ->with(['product','discount'])
                ->findOrFail($item->price_id);

            if ($price->product?->status !== 'active') {
                throw ValidationException::withMessages([
                    'product' => "El producto {$price->product_id} no está disponible para venta.",
                ]);
            }

            $unitsPerPrice = (int)($price->quantity ?? 1);  // p.ej. paquete de N unidades
            $unitsNeeded   = $qtyRequested * $unitsPerPrice;

            $unitsByProduct[$price->product_id] = ($unitsByProduct[$price->product_id] ?? 0) + $unitsNeeded;
        }

        // 2) Bloquear productos y descontar stock consolidado
        foreach ($unitsByProduct as $productId => $unitsToDeduct) {
            /** @var Product $product */
            $product = Product::query()
                ->lockForUpdate()
                ->findOrFail($productId);

            if ($product->available_quantity < $unitsToDeduct) {
                throw ValidationException::withMessages([
                    'stock' => "Sin stock suficiente para el producto {$product->id} (requiere {$unitsToDeduct}, hay {$product->available_quantity}).",
                ]);
            }

            $product->available_quantity -= $unitsToDeduct;
            $product->save();
        }

        // 3) Crear (o recuperar) la orden de forma idempotente por cart_id
        $order = Order::query()
            ->where('cart_id', $cart->id)
            ->lockForUpdate()
            ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => $cart->user_id,
                'cart_id' => $cart->id,
                // aquí podrías setear totales si los manejas en orders
            ]);
        }

        // 4) Consolidar items por price_id y crear snapshots en order_items
        $byPrice = []; // price_id => ['product_id','quantity','unit_price','currency']
        foreach ($cart->items as $item) {
            $qty   = (int)($item->quantity ?? 1);
            $price = $item->price; // eager-loaded

            if (!$price) { continue; }

            if (!isset($byPrice[$price->id])) {
                $byPrice[$price->id] = [
                    'product_id' => $price->product_id,
                    'quantity'   => 0,
                    'unit_price' => (int) $price->price,          // snapshot (centavos)
                    'currency'   => $cart->currency ?? 'USD',
                ];
            }
            $byPrice[$price->id]['quantity'] += $qty;
        }

        // Idempotencia simple: limpiar e insertar (o usa updateOrCreate si prefieres)
        OrderItem::query()->where('order_id', $order->id)->delete();

        $rows = [];
        foreach ($byPrice as $priceId => $p) {
            $rows[] = [
                'order_id'   => $order->id,
                'product_id' => $p['product_id'],
                'price_id'   => $priceId,
                'quantity'   => $p['quantity'],
                'unit_price' => $p['unit_price'],
                'currency'   => $p['currency'],
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        if (!empty($rows)) {
            OrderItem::query()->insert($rows);
        }

        // 5) Marcar carrito como checked_out y crear el siguiente activo
        $cart->status = CartStatus::CheckedOut;
        $cart->checked_out_at = $now;
        $cart->save();

        $this->cartService->getOrCreateActiveCart($cart->user_id);

        // 6) Retornar carrito con relaciones útiles
        return $cart->fresh(['items.price.product', 'items.price.discount']);
    });
}

    /**
     * Verifica stock para un price concreto.
     * - $qtyRequested: cantidad de ese price (default 1 si tus cart_items no tienen quantity).
     * - Valida que el price esté activo y que el product tenga stock suficiente.
     */
    public function checkStockByPrice(int $priceId, int $qtyRequested = 1): void
    {
        /** @var Price $price */
        $price = Price::query()
            ->with('product')
            ->where('status', 'active')
            ->findOrFail($priceId);

        if (!$price->product) {
            throw ValidationException::withMessages([
                'price' => "El price {$priceId} no tiene producto asociado.",
            ]);
        }

        // cuántas unidades reales del producto representa 1 “price”
        $unitsPerPrice = (int) ($price->quantity ?? 1);
        $unitsNeeded = $qtyRequested * $unitsPerPrice;

        $available = (int) $price->product->available_quantity;

        if ($unitsNeeded <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'La cantidad solicitada debe ser mayor que 0.',
            ]);
        }

        if ($available < $unitsNeeded) {
            throw ValidationException::withMessages([
                'stock' => "Stock insuficiente para el producto {$price->product->id} (requiere {$unitsNeeded}, hay {$available}).",
            ]);
        }
    }
}