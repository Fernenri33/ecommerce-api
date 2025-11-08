<?php

namespace App\Services;

use App\DTOs\DiscountDTO;
use App\DTOs\DiscountUpdateDTO;
use App\Models\Discount;

class DiscountService extends BaseService{
    public function __construct(){
        parent::__construct(new Discount, 'Descuento');
    }
    public function getAllDiscounts(){
        return $this->getAll();
    }
    public function findDiscountById($id){
        return $this->find($id);
    } 
    public function findProductByName($name){
        return $this->findByName($name);
    }
    public function createDiscount(DiscountDTO $discountDTO){
        return $this->create($discountDTO->toArray());
    }
    public function updateDiscount($id, DiscountUpdateDTO $discountUpdateDTO){
        return $this->update($id,$discountUpdateDTO->toArray());
    }
    public function deleteDiscount($id){
        return $this->delete($id);
    }
}