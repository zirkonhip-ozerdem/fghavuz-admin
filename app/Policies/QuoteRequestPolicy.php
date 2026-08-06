<?php

namespace App\Policies;

use App\Models\QuoteRequest;
use App\Models\User;
use App\Support\Permissions;

class QuoteRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can(Permissions::VIEW_QUOTE_REQUESTS);
    }

    public function view(User $user, QuoteRequest $record): bool
    {
        return $user->can(Permissions::VIEW_QUOTE_REQUESTS);
    }

    public function update(User $user, QuoteRequest $record): bool
    {
        return $user->can(Permissions::MANAGE_QUOTE_REQUESTS);
    }

    public function delete(User $user, QuoteRequest $record): bool
    {
        return $user->can(Permissions::MANAGE_QUOTE_REQUESTS);
    }
}
