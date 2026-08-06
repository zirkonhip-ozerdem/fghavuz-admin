<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ProductSubcategory;
use App\Support\Permissions;

class ProductSubcategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function view(User $user, ProductSubcategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function update(User $user, ProductSubcategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function delete(User $user, ProductSubcategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function restore(User $user, ProductSubcategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }

    public function forceDelete(User $user, ProductSubcategory $record): bool
    {
        return $user->can(Permissions::MANAGE_PRODUCTS);
    }
}
