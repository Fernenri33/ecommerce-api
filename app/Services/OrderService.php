<?php

namespace App\Services;

use App\DTOs\OrderDTO;
use App\DTOs\OrderUpdateDTO;
use App\Models\Order;

class OrderService extends BaseService{
    protected $with = ['cart'];

    public function __costruct(){
        parent::__construct(new Order,'Orden');
    }
    public function getAllOrders(){
        return $this->getAll(20);
    }
    public function findOrderById($id){
        return $this->find($id);
    }
    public function createOrder(OrderDTO $orderDTO){
        return $this->create($orderDTO->toArray());
    }
    public function updateOrder($id,OrderUpdateDTO $orderUpdateDTO){
        return $this->update($id,$orderUpdateDTO->toArray());
    }
    public function deleteOrder($id){
        return $this->delete($id);
    }
}