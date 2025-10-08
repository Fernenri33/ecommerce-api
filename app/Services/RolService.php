<?php

namespace App\Services;
use App\DTOs\RolDTO;
use App\DTOs\RolUpdateDTO;
use App\Models\Role;

class RolService extends BaseService{
    public function __construct(){
        parent::__construct(new Role, 'rol');
    }
    public function getAllRoles(){
        return $this->getAll(5);
    }
    public function createRol(RolDTO $rolDTO){
        return $this->create($rolDTO->toArray());
    }
    public function updateRol($id, RolUpdateDTO $rolUpdateDTO){
        return $this->update($id, $rolUpdateDTO->toArray());
    }
    public function deleteRol($id){
        return $this->delete($id);
    }
    public function findRolById($id){
        return $this->find($id);
    }
    public function findRolByName($name){
        return $this->findByName($name);
    }
}