<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Group;

Broadcast::channel('group.{groupId}', function ($user, $groupId) {
    $group = Group::find($groupId);
    if (!$group) return false;

    $isMember = $group->users()->where('users.id', $user->id)->exists();
    
    if (!$isMember) return false;

    return [
        'id'   => $user->id,
        'name' => $user->name,
    ];
});