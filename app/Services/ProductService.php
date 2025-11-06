<?php

namespace App\Services;
use App\DTOs\ProductDTO;
use App\DTOs\ProductUpdateDTO;
use App\Models\Product;

class ProductService extends BaseService{

    protected $with = ['unit'];

    public function __construct(){
        parent::__construct(new Product, 'producto');
    }
    public function getAllProducts (){
        return $this ->getAll(20);
    }
    public function createProduct(ProductDTO $productDTO){  
        return $this->create($productDTO->toArray());
    }
    public function updateProduct($id, ProductUpdateDTO $productUpdateDTO){
        return $this->update($id, $productUpdateDTO->toArray());
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