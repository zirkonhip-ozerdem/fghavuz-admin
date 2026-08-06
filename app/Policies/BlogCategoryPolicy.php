<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BlogCategory;
use App\Support\Permissions;

class BlogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function view(User $user, BlogCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function update(User $user, BlogCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function delete(User $user, BlogCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function restore(User $user, BlogCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function forceDelete(User $user, BlogCategory $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }
}
