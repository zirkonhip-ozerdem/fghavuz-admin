<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Support\Permissions;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function view(User $user, Product $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function update(User $user, Product $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function delete(User $user, Product $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function restore(User $user, Product $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function forceDelete(User $user, Product $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }
}
