<?php

namespace App\Policies;

use App\Models\Pin;
use App\Models\User;

class PinPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Pin $pin): bool
    {
        if ($pin->user_id === $user->id) {
            return true;
        }

        if ($pin->group_id) {
            return $user->groups()
                ->where('groups.id', $pin->group_id)
                ->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Pin $pin): bool
    {
        if ($pin->user_id === $user->id) {
            return true;
        }

        if ($pin->group_id) {
            return $user->groups()
                ->where('groups.id', $pin->group_id)
                ->exists();
        }

        return false;
    }

    public function delete(User $user, Pin $pin): bool
    {
        return $pin->user_id === $user->id;
    }
}