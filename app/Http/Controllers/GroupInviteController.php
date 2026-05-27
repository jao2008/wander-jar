<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\RedirectResponse;

class GroupInviteController extends Controller
{
    public function accept(string $code): RedirectResponse
    {
        $user = auth()->user();

        // 1) Encontrar o grupo pelo invite_code
        $group = Group::where('invite_code', $code)->firstOrFail();

        // 2) Se já pertence ao grupo, não volta a anexar
        if ($group->users()->whereKey($user->id)->exists()) {
            return redirect()
                ->route('groups.index')
                ->with('status', 'Já pertencias a este grupo ✅');
        }

        // 3) Entrar no grupo como member (pivot: role)
        $group->users()->attach($user->id, [
            'role' => 'member',
        ]);

        return redirect()
            ->route('groups.index')
            ->with('status', 'Entraste no grupo ✅');
    }
}
