<?php

namespace App\Services;

use App\DTOs\CategoryDTO;
use App\DTOs\CategoryUpdateDTO;
use App\Models\Category;

class CategoryService extends BaseService{
    public function __construc(){
        parent::__construct(new Category,'categoría');
    }
    public function getAllCategories(){
        return $this->getAll();
    }
    public function findCategoryByName($name){
        return $this->findByName($name);
    }
    public function findCategoryById($id){
        return $this->find($id);
    }
    public function createCategory(CategoryDTO $categoyDTO){
        return $this->create($categoyDTO->toArray());
    }
    public function updateCategory(CategoryUpdateDTO $categoryUpdateDTO){
        return $this->update($categoryUpdateDTO->toArray());
    }
}