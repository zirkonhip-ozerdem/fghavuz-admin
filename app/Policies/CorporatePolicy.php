<?php

namespace App\Policies;

use App\Models\Corporate;
use App\Models\User;
use App\Support\Permissions;

class CorporatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_CORPORATE);
    }

    public function view(User $user, Corporate $record): bool
    {
        return $user->can(Permissions::MANAGE_CORPORATE);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_CORPORATE);
    }

    public function update(User $user, Corporate $record): bool
    {
        return $user->can(Permissions::MANAGE_CORPORATE);
    }
}
