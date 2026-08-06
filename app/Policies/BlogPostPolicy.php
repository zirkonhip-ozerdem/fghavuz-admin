<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BlogPost;
use App\Support\Permissions;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function view(User $user, BlogPost $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function update(User $user, BlogPost $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function delete(User $user, BlogPost $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function restore(User $user, BlogPost $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }

    public function forceDelete(User $user, BlogPost $record): bool
    {
        return $user->can(Permissions::MANAGE_BLOG);
    }
}
