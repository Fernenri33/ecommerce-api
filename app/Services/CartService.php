<?php

namespace App\Services;

use App\DTOs\CartDTO;
use App\DTOs\CartUpdateDTO;
use App\Helpers\ResponseHelper;
use App\Models\Cart;
use App\Models\User;

class CartService extends BaseService
{

    protected $with = ['user'];

    public function __construct()
    {
        parent::__construct(new Cart, 'carrito');
    }
    public function getAllCarts()
    {
        return $this->getAll(20);
    }
    public function getCartById($id)
    {
        return $this->find($id);
    }
    public function findCartsByUser($userId)
    {

        try {
            $user = User::find($userId);

            if ($user->isEmpty()) {
                return ResponseHelper::notFound(
                    "usuario"
                );
            }
            $carts = Cart::where('user_id', '=', $userId)->get();

            if ($carts->isEmpty()) {
                return ResponseHelper::notFound("carritos para el usuario");
            }
            return ResponseHelper::success(
                "Carritos encontrados",
                $carts
            );

        } catch (\Exception $e) {
            return ResponseHelper::exception("buscar carritos por nombre de usuario", $e);
        }

    }
    public function createCart(CartDTO $cartDTO)
    {
        return $this->create($cartDTO->toArray());
    }
    public function updateCart($id, CartUpdateDTO $cartUpdateDTO)
    {
        return $this->update($id, $cartUpdateDTO->toArray());
    }
    public function deleteCart($id)
    {
        return $this->delete($id);
    }
}