<?php

namespace App\Services;

use App\DTOs\PriceDTO;
use App\DTOs\PriceUpdateDTO;
use App\Models\Price;

class PriceService extends BaseService{

    protected $with = ['product','discount'];

    public function __construct(){
        parent::__construct(new Price, 'precio');
    }
    public function getAllPrices(){
        return $this->getAll(20);
    }
    public function getPriceById($id){
        return $this->find($id);
    }
    public function findPriceByName($name){
        return $this->findByName($name);
    }
    public function createPrice(PriceDTO $priceDTO){
        return $this->create($priceDTO->toArray());
    }
    public function updatePrice($id, PriceUpdateDTO $priceUpdateDTO){
        return $this->update($id, $priceUpdateDTO->toArray());
    }
    public function deletePrice($id){
        return $this->delete($id);
    }
}