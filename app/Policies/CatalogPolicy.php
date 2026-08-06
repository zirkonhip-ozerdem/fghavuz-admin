<?php

namespace App\Policies;

use App\Models\Catalog;
use App\Models\User;
use App\Support\Permissions;

class CatalogPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function view(User $user, Catalog $record): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function update(User $user, Catalog $record): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function delete(User $user, Catalog $record): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function restore(User $user, Catalog $record): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }

    public function forceDelete(User $user, Catalog $record): bool
    {
        return $user->can(Permissions::MANAGE_CATALOGS);
    }
}
