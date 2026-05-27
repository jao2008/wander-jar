<?php

namespace App\Http\Middleware;

use App\Models\Group;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGroupMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $groupParam = $request->route('group');

        $group = $groupParam instanceof Group
            ? $groupParam
            : Group::find($groupParam);

        if (!$group) {
            abort(404);
        }

        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        $isMember = $user->groups()
            ->where('groups.id', $group->id)
            ->exists();

        abort_unless($isMember, 403, 'Não tens acesso a este grupo.');

        return $next($request);
    }
}