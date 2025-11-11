<?php

namespace App\Http\Controllers;

use App\DTOs\CartBulkUpdateDTO;
use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CartItemService;
use App\Services\CartService;
use App\Services\EcommerceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EcommerceController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected CartService $cartService,
        protected CartItemService $cartItemService,
        protected EcommerceService $ecommerceService,
    ) {

    }

    /**
     * POST /ecommerce
     * Agregar item al carrito activo (consolidación por price_id).
     * body: price_id (int), quantity (int>=1)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'price_id' => 'required|integer|exists:prices,id',
            'quantity' => 'nullable|integer|min:1',
        ]);

        $user = $request->user();
        $cart = $this->cartService->getOrCreateActiveCart($user->id);

        $this->authorize('update', $cart);

        // Si usas DTO: new CartItemDTO([...])
        $item = $this->cartItemService->createCartItem((object) [
            'cart_id' => $cart->id,
            'price_id' => (int) $validated['price_id'],
            'quantity' => (int) ($validated['quantity'] ?? 1),
        ]);

        // refrescar carrito completo
        $cart->refresh()->load(['items.price.product', 'items.price.discount']);

        return response()->json([
            'success' => true,
            'message' => 'Producto agregado al carrito',
            'data' => $cart,
        ], 201);
    }

    /**
     * PUT /ecommerce/{cart}
     * Actualización BULK del carrito + items.
     * body: notes?, currency?, mode?=merge|replace, items[] = [{item_id?,price_id,quantity,unit_price?}]
     */
    public function update(Request $request, Cart $cart)
    {
        $this->authorize('update', $cart);

        $validated = $request->validate([
            'mode' => 'nullable|in:merge,replace',
            'items' => 'nullable|array',
            'items.*.item_id' => 'nullable|integer|exists:cart_items,id',
            'items.*.price_id' => 'required|integer|exists:prices,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_price' => 'nullable|integer|min:0',
        ]);

        $dto = CartBulkUpdateDTO::fromArray([
            'items' => $validated['items'] ?? [],
        ]);

        // Si tienes CartBulkUpdateDTO, reemplaza el objeto anónimo por el DTO
        $cart = $this->cartService->updateCartWithItems(
            cartId: $cart->id,
            dto: $dto,
            mode: $validated['mode'] ?? 'merge'
        );

        return response()->json([
            'success' => true,
            'message' => 'Carrito actualizado',
            'data' => $cart->load(['items.price.product', 'items.price.discount']),
        ]);
    }

    /**
     * POST /ecommerce/{cart}/checkout
     * Ejecuta el checkout (crea la Orden, descuenta stock, cierra el carrito y crea el siguiente).
     */
    public function checkout(Request $request, Cart $cart)
    {
        $this->authorize('checkout', $cart);

        // El EcommerceService hace todo (locks, validaciones, order & order_items)
        $resultCart = $this->ecommerceService->checkoutCart($cart->id);

        return response()->json([
            'success' => true,
            'message' => 'Checkout exitoso',
            'data' => $resultCart,
        ]);
    }
}
