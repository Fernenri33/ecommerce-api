<?php

namespace App\Services;

use App\DTOs\UserDTO;
use App\DTOs\UserUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\User;

class UserService extends BaseService
{
    public function __construct()
    {
        parent::__construct(new User, 'Usuario');
    }
    public function getAllUsers()
    {
        return $this->getAll();
    }
    public function findUserById($id)
    {
        return $this->find($id);
    }
    public function findUserByName($name)
    {
        try {
            $users = User::where('name', 'LIKE', "%{$name}%")
                ->orWhere('last_name', 'LIKE', "%{$name}%")
                ->get();
            if ($users->isEmpty()) {
                return ResponseHelper::notFound('Usuario');
            }
            return ResponseHelper::success(
                "Usuarios encontrados exitosamente",
                $users
            );
        } catch (\Exception $e) {
            return ResponseHelper::exception("Error al buscar {$this->resourceName}s", $e);
        }
    }
    public function createUser(UserDTO $userDTO){
        return $this->create($userDTO->toArray());
    }
    public function updateUser(UserUpdateDTO $userUpdateDTO, $id){
        return $this->update($id, $userUpdateDTO->toArray());
    }
    public function deleteUser($id){
        return $this->delete($id);
    }
}