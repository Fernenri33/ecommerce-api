<?php

namespace App\Services;
use App\DTOs\UnitDTO;
use App\Models\Unit;

Class UnitService extends BaseService{
    public function __construct(){
        parent::__construct(new Unit, 'unidad');
    }
    public function getAllUnits($perPage = 10){
        return $this->getAll($perPage);
    }
    public function findUnitByName($name){
        return $this->findByName($name);
    }
    public function findUnitById($id){
        return $this->find($id);
    }
    public function createUnit(UnitDTO $unitDTO){
        return $this->create($unitDTO->toArray());
    }
    public function updateUnit($id, UnitDTO $unitDTO){
        return $this->update($id, $unitDTO->toArray());
    }
    public function deleteUnit($id){
        return $this->delete($id);
    }
}