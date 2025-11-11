<?php

namespace App\Policies;

use App\Models\CartItem;
use App\Models\User;

class CartItemPolicy
{
    public function view(User $user, CartItem $item): bool
    {
        // Evita N+1 en autorización: carga 'cart' antes de autorizar
        $cart = $item->relationLoaded('cart') ? $item->cart : $item->cart()->first();
        return $cart && $cart->user_id === $user->id;
    }

    public function update(User $user, CartItem $item): bool
    {
        $cart = $item->relationLoaded('cart') ? $item->cart : $item->cart()->first();
        return $cart && $cart->user_id === $user->id;
    }

    public function delete(User $user, CartItem $item): bool
    {
        $cart = $item->relationLoaded('cart') ? $item->cart : $item->cart()->first();
        return $cart && $cart->user_id === $user->id;
    }

    // crear ítems suele autorizarse contra el Cart (no contra CartItem)
}
