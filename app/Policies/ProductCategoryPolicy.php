<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductCategory;
use App\Support\Permissions;

class ProductCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function view(User $user, ProductCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function update(User $user, ProductCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function delete(User $user, ProductCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function restore(User $user, ProductCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function forceDelete(User $user, ProductCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }
}
