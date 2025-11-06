<?php

namespace App\Services;

use App\DTOs\CartItemDTO;
use App\DTOs\CartItemUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\CartItem;

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
                    "Item"
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

        }
    }
    public function createCartItem(CartItemDTO $cartItemDTO){
        return $this->create($cartItemDTO->toArray());
    }
    public function updateCartItem($id, CartItemUpdateDTO $cartItemUpdateDTO){
        return $this->update($id, $cartItemUpdateDTO->toArray());
    }
    public function deleteCartImtem($id){
        return $this->delete($id);
    }
}