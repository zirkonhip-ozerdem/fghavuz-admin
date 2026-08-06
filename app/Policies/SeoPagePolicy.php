<?php

namespace App\Policies;

use App\Models\SeoPage;
use App\Models\User;
use App\Support\Permissions;

class SeoPagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_SEO);
    }

    public function view(User $user, SeoPage $seoPage): bool
    {
        return $user->can(Permissions::MANAGE_SEO);
    }

    public function create(User $user): bool
    {
        return $user->can(Permissions::MANAGE_SEO);
    }

    public function update(User $user, SeoPage $seoPage): bool
    {
        return $user->can(Permissions::MANAGE_SEO);
    }

    public function delete(User $user, SeoPage $seoPage): bool
    {
        return $user->can(Permissions::MANAGE_SEO);
    }
}
