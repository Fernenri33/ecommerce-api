<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
    public function viewAny(?User $user): bool
    {
        return true; // Todos pueden ver
    }

    public function view(?User $user): bool 
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->rol_id === 1;
    }

    public function update(User $user): bool 
    {
        return $user->rol_id === 1;
    }

    public function delete(User $user): bool
    {
        return $user->rol_id === 1;
    }

    public function restore(User $user): bool
    {
        return $user->rol_id === 1;
    }

    public function forceDelete(User $user): bool
    {
        return false;
    }
}