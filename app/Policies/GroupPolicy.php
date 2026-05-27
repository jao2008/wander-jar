<?php

namespace App\Policies;

use App\Models\Group;
use App\Models\User;

class GroupPolicy
{
    public function view(User $user, Group $group): bool
    {
        return $group->users()->where('users.id', $user->id)->exists();
    }

    public function viewMap(User $user, Group $group): bool
    {
        return $group->users()->where('users.id', $user->id)->exists();
    }

    public function viewChat(User $user, Group $group): bool
    {
        return $group->users()->where('users.id', $user->id)->exists();
    }

    public function sendMessage(User $user, Group $group): bool
    {
        return $group->users()->where('users.id', $user->id)->exists();
    }

    public function manage(User $user, Group $group): bool
    {
        $membership = $group->users()
            ->where('users.id', $user->id)
            ->first();

        return ($membership?->pivot?->role ?? null) === 'admin';
    }
}