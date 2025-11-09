<?php

namespace App\Services;

use App\DTOs\SubcategoryDTO;
use App\DTOs\SubcategoryUpdateDTO;
use App\Models\Subcategory;

class SubcategoryService extends BaseService{
    public function __construct(){
        parent::__construct(new Subcategory, 'Subcategoría');
    
    }
    public function getAllSubcategories(){
        return $this->getAll();
    }
    public function findSubcategoryById($id){
        return $this->find($id);
    }
    public function findSubcategoryByName($name){
        return $this->findByName($name);
    }
    public function createSubCategory(SubcategoryDTO $subcategoryDTO){
        return $this->create($subcategoryDTO->toArray());
    }
    public function updateSubCategory($id, SubcategoryUpdateDTO $subcategoryDTO){
        return $this->update($id, $subcategoryDTO->toArray());
    }
    public function deleteSubcategory($id){
        return $this->delete($id);
    }
}