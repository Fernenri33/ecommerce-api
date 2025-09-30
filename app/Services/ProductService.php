<?php

namespace App\Services;
use App\DTOs\ProductDTO;
use App\Models\Product;

class ProductService extends BaseService{

    protected $with = ['unit:id,name,description'];

    public function __construct(){
        parent::__construct(new Product, 'producto');
    }
    public function getAllProducts (){
        return $this ->getAll(10);
    }
    public function createProduct(ProductDTO $productDTO){  
        return $this->create($productDTO->toArray());
    }
    public function deleteProduct($id){
        return $this->delete($id);
    }
    public function findProductById ($id){
        return $this->find($id);
    }
    public function findProductByName($name){
        return $this->findByName($name);
    }
}