<?php

namespace App\Services;

use App\DTOs\OrderItemDTO;
use App\DTOs\OrderUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\Order;
use App\Models\OrderItem;

class OrderItemService extends BaseService{
    protected $with = ['order'];

    public function __costruct(){
        parent::__construct(new OrderItem,'Orden');
    }
    public function getAllOrderItems(){
        return $this->getAll();
    }
    public function getAllItemsByOrder($id){
        try{
            $cart = Order::find($id);
            if($cart->isEmpty()){
                return ResponseHelper::notFound(
                    "Orden"
                );
            }
            $items = Orderitem::where('order_id', '=', $id)->get();

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
    public function creteOrderItem(OrderItemDTO $orderItemDTO){
        return $this->create($orderItemDTO->toArray());
    }
    public function updateOrderitem($id, OrderUpdateDTO $orderUpdateDTO){
        return $this->update($id, $orderUpdateDTO->toArray());
    }
    public function deleteCartItem($id){
        return $this->delete($id);
    }
}