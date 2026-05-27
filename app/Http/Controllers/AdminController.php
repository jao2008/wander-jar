<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Group;
use App\Models\Pin;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $stats = [
            'users'         => User::count(),
            'groups'        => Group::count(),
            'pins'          => Pin::count(),
            'events'        => Event::count(),
            'personal_pins' => Pin::whereNull('group_id')->count(),
            'group_pins'    => Pin::whereNotNull('group_id')->count(),
            'admins'        => User::where('is_admin', true)->count(),
        ];

        $quickStats = [
            'new_users_7d'  => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_groups_7d' => Group::where('created_at', '>=', now()->subDays(7))->count(),
            'new_pins_7d'   => Pin::where('created_at', '>=', now()->subDays(7))->count(),
            'new_events_7d' => Event::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $latestUsers = User::latest()
            ->take(6)
            ->get(['id', 'name', 'email', 'is_admin', 'created_at']);

        $latestGroups = Group::withCount(['users', 'pins'])
            ->latest()
            ->take(6)
            ->get(['id', 'name', 'privacy', 'created_at']);

        $latestPins = Pin::with(['user:id,name', 'group:id,name'])
            ->latest()
            ->take(8)
            ->get(['id', 'user_id', 'group_id', 'title', 'location_text', 'created_at']);

        $latestEvents = Event::with('creator:id,name')
            ->withCount('participants')
            ->latest()
            ->take(6)
            ->get([
                'id',
                'user_id',
                'title',
                'location_text',
                'event_date',
                'event_time',
                'max_participants',
                'is_active',
                'created_at',
            ]);

        return view('admin.dashboard', compact(
            'stats',
            'quickStats',
            'latestUsers',
            'latestGroups',
            'latestPins',
            'latestEvents'
        ));
    }

    public function users(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users', compact('users', 'search'));
    }

    public function makeAdmin(User $user): RedirectResponse
    {
        abort_if($user->is($this->authUser()), 403, 'Não podes alterar o teu próprio papel.');

        $user->update([
            'is_admin' => true,
        ]);

        return back()->with('success', "{$user->name} é agora administrador.");
    }

    public function removeAdmin(User $user): RedirectResponse
    {
        abort_if($user->is($this->authUser()), 403, 'Não podes alterar o teu próprio papel.');

        $user->update([
            'is_admin' => false,
        ]);

        return back()->with('success', "Permissões de administrador removidas de {$user->name}.");
    }

    public function destroyUser(User $user): RedirectResponse
    {
        abort_if($user->is($this->authUser()), 403, 'Não podes apagar a tua própria conta.');

        $name = $user->name;

        $user->delete();

        return back()->with('success', "Utilizador {$name} apagado com sucesso.");
    }

    public function groups(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $groups = Group::query()
            ->withCount(['users', 'pins'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.groups', compact('groups', 'search'));
    }

    public function destroyGroup(Group $group): RedirectResponse
    {
        $name = $group->name;

        $group->delete();

        return back()->with('success', "Grupo {$name} apagado com sucesso.");
    }

    public function pins(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $pins = Pin::with(['user:id,name,email', 'group:id,name'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('location_text', 'like', "%{$search}%")
                        ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pins', compact('pins', 'search'));
    }

    public function destroyPin(Pin $pin): RedirectResponse
    {
        $title = $pin->title ?? 'Sem título';

        $pin->delete();

        return back()->with('success', "Pin \"{$title}\" apagado com sucesso.");
    }

    public function events(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));

        $events = Event::query()
            ->with('creator:id,name,email')
            ->withCount('participants')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($sub) use ($search) {
                    $sub->where('title', 'like', "%{$search}%")
                        ->orWhere('location_text', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.events', compact('events', 'search'));
    }

    public function cancelEvent(Event $event): RedirectResponse
    {
        if (Schema::hasColumn('events', 'is_active')) {
            $event->update([
                'is_active' => false,
            ]);

            return back()->with('success', "Evento \"{$event->title}\" cancelado.");
        }

        return back()->with('success', 'Este projeto ainda não tem a coluna is_active nos eventos.');
    }

    public function destroyEvent(Event $event): RedirectResponse
    {
        $title = $event->title ?? 'Sem título';

        $event->delete();

        return back()->with('success', "Evento \"{$title}\" apagado com sucesso.");
    }

    private function authUser(): User
    {
        /** @var User $user */
        $user = auth()->user();

        return $user;
    }
}