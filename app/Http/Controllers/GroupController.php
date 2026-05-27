<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupStoreRequest;
use App\Models\Group;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(): View
    {
        $groups = auth()->user()->groups()
            ->withPivot('role')
            ->withCount('pins')
            ->withCount('users')
            ->orderBy('name')
            ->get();

        $stats = [
            'total'   => $groups->count(),
            'admins'  => $groups->filter(fn ($g) => (($g->pivot->role ?? 'member') === 'admin'))->count(),
            'members' => $groups->filter(fn ($g) => (($g->pivot->role ?? 'member') === 'member'))->count(),
            'pins'    => (int) $groups->sum('pins_count'),
        ];

        return view('groups.index', compact('groups', 'stats'));
    }

    public function create(): View
    {
        return view('groups.create');
    }

    public function store(GroupStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $data['invite_code'] = strtoupper(Str::random(10));

        $group = Group::create($data);

        $group->users()->attach(auth()->id(), [
            'role' => 'admin',
        ]);

        return redirect()
            ->route('groups.index')
            ->with('status', 'Grupo criado com sucesso.');
    }

    public function joinByInvite(string $invite_code): RedirectResponse
    {
        $group = Group::where('invite_code', $invite_code)->firstOrFail();

        $alreadyMember = $group->users()
            ->where('users.id', auth()->id())
            ->exists();

        if (!$alreadyMember) {
            $group->users()->attach(auth()->id(), [
                'role' => 'member',
            ]);
        }

        return redirect()
            ->route('groups.index')
            ->with(
                'status',
                $alreadyMember
                    ? 'Já fazes parte deste grupo.'
                    : 'Entraste no grupo com sucesso.'
            );
    }

    public function edit(Group $group): View
    {
        $this->ensureGroupAdmin($group);

        return view('groups.edit', compact('group'));
    }

    public function update(Request $request, Group $group): RedirectResponse
    {
        $this->ensureGroupAdmin($group);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $group->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('groups.index')
            ->with('status', 'Grupo atualizado com sucesso.');
    }

    public function map(Group $group): View
    {
        $role = $this->ensureGroupMember($group);
        $membersCount = $group->users()->count();

        $pins = $group->pins()
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->latest()
            ->get([
                'id',
                'group_id',
                'title',
                'content',
                'location_text',
                'lat',
                'lng',
                'image_path',
                'created_at',
            ]);

        return view('groups.map', compact(
            'group',
            'role',
            'membersCount',
            'pins'
        ));
    }

    public function members(Group $group): View
    {
        $role = $this->ensureGroupMember($group);

        $members = $group->users()
            ->withPivot('role')
            ->orderByRaw("CASE WHEN group_user.role = 'admin' THEN 0 ELSE 1 END")
            ->orderBy('name')
            ->get();

        $adminsCount = $members
            ->filter(fn ($member) => (($member->pivot->role ?? 'member') === 'admin'))
            ->count();

        return view('groups.members', compact(
            'group',
            'members',
            'role',
            'adminsCount'
        ));
    }

    public function promoteMember(Group $group, User $user): RedirectResponse
    {
        $this->ensureGroupAdmin($group);

        $targetMember = $this->getGroupMember($group, $user);

        if (($targetMember->pivot->role ?? 'member') === 'admin') {
            return back()->with('status', 'Este utilizador já é admin.');
        }

        $group->users()->updateExistingPivot($user->id, [
            'role' => 'admin',
        ]);

        return back()->with('status', "{$user->name} foi promovido a admin.");
    }

    public function demoteMember(Group $group, User $user): RedirectResponse
    {
        $this->ensureGroupAdmin($group);

        if ($user->id === auth()->id()) {
            return back()->with('status', 'Não podes remover as tuas próprias permissões de admin.');
        }

        $targetMember = $this->getGroupMember($group, $user);

        if (($targetMember->pivot->role ?? 'member') !== 'admin') {
            return back()->with('status', 'Este utilizador já é membro.');
        }

        $adminsCount = $group->users()
            ->wherePivot('role', 'admin')
            ->count();

        if ($adminsCount <= 1) {
            return back()->with('status', 'O grupo precisa de ter pelo menos um admin.');
        }

        $group->users()->updateExistingPivot($user->id, [
            'role' => 'member',
        ]);

        return back()->with('status', "{$user->name} passou a membro.");
    }

    public function removeMember(Group $group, User $user): RedirectResponse
    {
        $this->ensureGroupAdmin($group);

        if ($user->id === auth()->id()) {
            return back()->with('status', 'Não podes expulsar-te a ti própria do grupo.');
        }

        $targetMember = $this->getGroupMember($group, $user);
        $targetRole = $targetMember->pivot->role ?? 'member';

        if ($targetRole === 'admin') {
            $adminsCount = $group->users()
                ->wherePivot('role', 'admin')
                ->count();

            if ($adminsCount <= 1) {
                return back()->with('status', 'Não podes remover o único admin do grupo.');
            }
        }

        $group->users()->detach($user->id);

        return back()->with('status', "{$user->name} foi removido do grupo.");
    }

    private function getGroupMember(Group $group, User $user): User
    {
        $targetMember = $group->users()
            ->where('users.id', $user->id)
            ->withPivot('role')
            ->first();

        abort_unless($targetMember, 404);

        return $targetMember;
    }

    private function ensureGroupMember(Group $group): string
    {
        $membership = $group->users()
            ->where('users.id', auth()->id())
            ->withPivot('role')
            ->first();

        abort_unless($membership, 403);

        return $membership?->pivot?->role ?? 'member';
    }

    private function ensureGroupAdmin(Group $group): string
    {
        $role = $this->ensureGroupMember($group);

        abort_unless($role === 'admin', 403);

        return $role;
    }
}