<?php

namespace App\Policies;

use App\Models\SiteSetting;
use App\Models\User;
use App\Support\Permissions;

class SiteSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::MANAGE_SITE_SETTINGS);
    }

    public function view(User $user, SiteSetting $siteSetting): bool
    {
        return $user->can(Permissions::MANAGE_SITE_SETTINGS);
    }

    public function update(User $user, SiteSetting $siteSetting): bool
    {
        return $user->can(Permissions::MANAGE_SITE_SETTINGS);
    }
}
