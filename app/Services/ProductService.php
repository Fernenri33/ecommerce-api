<?php

namespace App\Services;
use App\DTOs\ProductDTO;
use App\DTOs\ProductUpdateDTO;
use App\Models\Product;
use DB;

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
    // Servicios relacionados con Subcategorias
    public function addSubcategories(int $productId, array $subcategoryIds): Product
    {
         return DB::transaction(function () use ($productId,$subcategoryIds) {
            $product = Product::findOrFail($productId);
            // valida tenant/tienda y que los IDs existan
            $product->subcategories()->syncWithoutDetaching($subcategoryIds);
            return $product->load('subcategories');
        });
    }
    public function removeSubcategory(int $productId, int $subcategoryId): void
    {
        DB::transaction(function () use ($productId,$subcategoryId) {
            Product::findOrFail($productId)
                ->subcategories()->detach($subcategoryId);
        });
    }
}