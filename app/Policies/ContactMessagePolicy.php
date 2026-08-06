<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use App\Support\Permissions;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::VIEW_CONTACT_MESSAGES);
    }

    public function view(User $user, ContactMessage $record): bool
    {
        return $user->can(Permissions::VIEW_CONTACT_MESSAGES);
    }

    public function update(User $user, ContactMessage $record): bool
    {
        return $user->can(Permissions::MANAGE_CONTACT_MESSAGES);
    }

    public function delete(User $user, ContactMessage $record): bool
    {
        return $user->can(Permissions::MANAGE_CONTACT_MESSAGES);
    }
}
