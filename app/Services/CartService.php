<?php

namespace App\Services;

use App\DTOs\CartBulkUpdateDTO;
use App\DTOs\CartDTO;
use App\DTOs\CartUpdateDTO;
use App\Enums\CartStatus;
use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;

use DB;

class CartService extends BaseService
{

    protected $with = ['user'];

    public function __construct()
    {
        parent::__construct(new Cart, 'carrito');
    }
    public function getAllCarts()
    {
        return $this->getAll(20);
    }
    public function getCartById($id)
    {
        return $this->find($id);
    }
    public function findCartsByUser($userId)
    {
        try {
            $carts = Cart::where('user_id', $userId)
                ->paginate(20); // 15 items por página por defecto

            if ($carts->isEmpty()) {
                return ResponseHelper::notFound("carritos para el usuario");
            }

            return ResponseHelper::success(
                "Carritos encontrados",
                $carts
            );

        } catch (\Exception $e) {
            return ResponseHelper::exception("buscar carritos por usuario", $e);
        }
    }
    public function createCart(CartDTO $cartDTO)
    {
        return $this->create($cartDTO->toArray());
    }
    public function updateCart($id, CartUpdateDTO $cartUpdateDTO)
    {
        return $this->update($id, $cartUpdateDTO->toArray());
    }
    public function deleteCart($id)
    {
        return $this->delete($id);
    }
    public function getOrCreateActiveCart(int $userId): Cart
    {
        return DB::transaction(function () use ($userId) {
            // Bloquea carritos del usuario para evitar carreras
            $cart = Cart::query()
                ->where('user_id', $userId)
                ->where('status', CartStatus::Active)
                ->lockForUpdate()
                ->first();

            if ($cart)
                return $cart;

            return Cart::create([
                'user_id' => $userId,
                'status' => CartStatus::Active,
            ]);
        });
    }
    public function ensureSingleActiveCart(int $userId): void
    {
        DB::transaction(function () use ($userId) {
            $carts = Cart::query()
                ->where('user_id', $userId)
                ->where('status', CartStatus::Active)
                ->lockForUpdate()
                ->get();

            if ($carts->count() <= 1)
                return;

            // Mantén el más reciente y cierra el resto
            $keep = $carts->sortByDesc('id')->shift();
            foreach ($carts as $c) {
                $c->status = CartStatus::Cancelled;
                $c->save();
            }
        });
    }
    public function updateCartWithItems(int $cartId, CartBulkUpdateDTO $dto, string $mode = 'merge'): Cart
    {
        return DB::transaction(function () use ($cartId, $dto, $mode) {
            /** @var Cart $cart */
            $cart = Cart::query()
                ->lockForUpdate()
                ->with('items') // usar la relación "items" (ver ajuste en modelos abajo)
                ->findOrFail($cartId);

            // Actualiza campos del carrito si existen
            $payload = $dto->toCartArray();
            if (!empty($payload)) {
                $cart->fill($payload)->save();
            }

            $incoming = $dto->items; // array de items
            if (empty($incoming)) {
                return $cart->fresh(['items.price.product', 'items.price.discount']);
            }

            // Mapa de actuales por price_id (asumiendo unique(cart_id, price_id))
            $currentByPrice = $cart->items->keyBy('price_id');

            $toUpsert = [];        // filas para upsert
            $toDeleteIds = [];     // deletes solicitados (quantity==0)
            $incomingPriceIdsPositives = []; // para modo replace

            foreach ($incoming as $row) {
                $priceId = (int) $row['price_id'];
                $qty = max(0, (int) ($row['quantity'] ?? 0));

                if ($qty === 0) {
                    // eliminar por item_id si viene, o por price_id si existe actualmente
                    if (!empty($row['item_id'])) {
                        $toDeleteIds[] = (int) $row['item_id'];
                    } elseif (isset($currentByPrice[$priceId])) {
                        $toDeleteIds[] = (int) $currentByPrice[$priceId]->id;
                    }
                    continue;
                }

                $incomingPriceIdsPositives[] = $priceId;

                $existing = $currentByPrice[$priceId] ?? null;

                $toUpsert[] = [
                    'id' => $existing?->id,         // null crea
                    'cart_id' => $cart->id,
                    'price_id' => $priceId,
                    'quantity' => $qty,
                    'updated_at' => now(),
                    'created_at' => $existing?->created_at ?? now(),
                ];
            }

            // UPSERT por (cart_id, price_id)
            if (!empty($toUpsert)) {
                CartItem::query()->upsert(
                    $toUpsert,
                    ['cart_id', 'price_id'],            // clave única
                    ['quantity', 'updated_at']          // columnas a actualizar
                );
            }

            // DELETEs explícitos (quantity==0)
            if (!empty($toDeleteIds)) {
                CartItem::query()
                    ->where('cart_id', $cart->id)
                    ->whereIn('id', $toDeleteIds)
                    ->delete();
            }

            // Modo replace: eliminar todo lo que no vino con quantity>0
            if ($mode === 'replace') {
                $incomingPriceIdsPositives = array_values(array_unique($incomingPriceIdsPositives));
                CartItem::query()
                    ->where('cart_id', $cart->id)
                    ->when(
                        !empty($incomingPriceIdsPositives),
                        fn($q) =>
                        $q->whereNotIn('price_id', $incomingPriceIdsPositives)
                    )
                    ->delete();
            }

            return $cart->fresh(['items.price.product', 'items.price.discount']);
        });
    }
}