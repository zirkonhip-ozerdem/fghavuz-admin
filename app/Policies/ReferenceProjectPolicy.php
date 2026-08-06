<?php

namespace App\Policies;

use App\Models\ReferenceProject;
use App\Models\User;
use App\Support\Permissions;

class ReferenceProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function view(User $user, ReferenceProject $record): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function update(User $user, ReferenceProject $record): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function delete(User $user, ReferenceProject $record): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function restore(User $user, ReferenceProject $record): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }

    public function forceDelete(User $user, ReferenceProject $record): bool
    {
        return $user->can(Permissions::MANAGE_REFERENCES);
    }
}
