<?php

namespace App\Services;

use App\DTOs\CartItemDTO;
use App\DTOs\CartItemUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\CartItem;
use DB;

Class CartItemService extends BaseService{
    protected $with = ['cart'];

    public function __construct()
    {
        parent::__construct(new CartItem, 'Item');
    }
    public function getAllCartItems(){
        return $this->getAll();
    }
    public function getAllItemsByCart($cartId){
        try{
            $cart = Cart::find($cartId);
            if($cart->isEmpty()){
                return ResponseHelper::notFound(
                    "Carrito"
                );
            }
            $items = CartItem::where('cart_id', '=', $cartId)->get();

            if($items->isEmpty()){
                return ResponseHelper::notFound("Items");
            }
            return ResponseHelper::success(
                "Items encontrados",
                $items
            );
        } catch(\Exception){
            return ResponseHelper::error("Ha ocurrido un error");
        }
    }
    public function createCartItem(CartItemDTO $dto)
{
    return DB::transaction(function () use ($dto) {
        $cartId  = (int) $dto->cart_id;
        $priceId = (int) $dto->price_id;
        $qty     = max(1, (int) ($dto->quantity ?? 1));

        // Bloquea posibles filas del mismo (cart, price) para evitar carreras
        $existing = CartItem::query()
            ->where('cart_id', $cartId)
            ->where('price_id', $priceId)
            ->lockForUpdate()
            ->first();

        if ($existing) {
            // Incrementa cantidad en la MISMA fila
            $existing->quantity = (int) $existing->quantity + $qty;
            $existing->save();

            return $existing->fresh(['price.product']);
        }

        // Si no existe, crea la fila
        $item = CartItem::create([
            'cart_id'   => $cartId,
            'price_id'  => $priceId,
            'quantity'  => $qty,
            // otros campos si tienes (currency, unit_price snapshot, etc.)
        ]);

        return $item->load('price.product');
    });
}
    public function updateCartItem($id, CartItemUpdateDTO $dto)
{
    return DB::transaction(function () use ($id, $dto) {
        $item = CartItem::query()->lockForUpdate()->findOrFail($id);

        // Si quantity <= 0, elimina
        $qty = (int) ($dto->quantity ?? 0);
        if ($qty <= 0) {
            $item->delete();
            return $item; // o null, según tu contrato
        }

        $item->quantity = $qty;
        $item->save();

        return $item->fresh(['price.product']);
    });
}

    public function deleteCartImtem($id){
        return $this->delete($id);
    }
}