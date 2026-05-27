<?php

namespace App\Http\Controllers;

use App\Http\Requests\PinStoreRequest;
use App\Models\Pin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PinController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Pin::class);

        $q = trim((string) $request->query('q', ''));
        $scope = (string) $request->query('scope', '');

        $pinsQuery = Pin::query()
            ->with(['group', 'user'])
            ->where('user_id', auth()->id());

        if ($q !== '') {
            $pinsQuery->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('location_text', 'like', "%{$q}%")
                    ->orWhere('content', 'like', "%{$q}%");
            });
        }

        if ($scope === 'pessoal') {
            $pinsQuery->whereNull('group_id');
        } elseif ($scope === 'grupo') {
            $pinsQuery->whereNotNull('group_id');
        }

        $pins = $pinsQuery
            ->latest()
            ->paginate(9)
            ->withQueryString();

        return view('pins.index', compact('pins', 'q', 'scope'));
    }

    public function map(): View
    {
        $this->authorize('viewAny', Pin::class);

        $pins = Pin::query()
            ->with(['group', 'user'])
            ->where('user_id', auth()->id())
            ->whereNull('group_id')
            ->latest()
            ->get([
                'id',
                'user_id',
                'group_id',
                'title',
                'content',
                'location_text',
                'lat',
                'lng',
                'image_path',
                'created_at',
            ]);

        return view('mapa.map', compact('pins'));
    }

    public function create(): View
    {
        $this->authorize('create', Pin::class);

        $groups = auth()->user()
            ->groups()
            ->orderBy('name')
            ->get();

        return view('pins.create', compact('groups'));
    }

    public function store(PinStoreRequest $request): RedirectResponse
    {
        $this->authorize('create', Pin::class);

        $data = $request->validated();

        $groupId = $this->normalizeGroupId($data['group_id'] ?? null);

        if ($groupId) {
            $belongs = auth()->user()
                ->groups()
                ->where('groups.id', $groupId)
                ->exists();

            abort_unless($belongs, 403, 'Não tens acesso a esse grupo.');
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('pins', 'public');
        }

        $pin = Pin::create([
            'user_id'       => auth()->id(),
            'group_id'      => $groupId,
            'title'         => $data['title'],
            'content'       => $data['content'] ?? null,
            'location_text' => $data['location_text'] ?? null,
            'lat'           => $data['lat'] ?? null,
            'lng'           => $data['lng'] ?? null,
            'image_path'    => $imagePath,
        ]);

        return redirect()
            ->route('pins.show', $pin)
            ->with('status', 'Pin criado com sucesso!');
    }

    public function show(Pin $pin): View
    {
        $this->authorize('view', $pin);

        $pin->load(['group', 'user']);

        return view('pins.show', compact('pin'));
    }

    public function edit(Pin $pin): View
    {
        $this->authorize('update', $pin);

        $pin->load(['group', 'user']);

        $groups = auth()->user()
            ->groups()
            ->orderBy('name')
            ->get();

        return view('pins.edit', compact('pin', 'groups'));
    }

    public function update(Request $request, Pin $pin): RedirectResponse
    {
        $this->authorize('update', $pin);

        $data = $request->validate([
            'group_id'      => ['nullable', 'integer', 'exists:groups,id'],
            'title'         => ['required', 'string', 'max:120'],
            'content'       => ['nullable', 'string', 'max:5000'],
            'location_text' => ['nullable', 'string', 'max:255'],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],
            'image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_image'  => ['nullable', 'boolean'],
        ]);

        $groupId = $this->normalizeGroupId($data['group_id'] ?? null);

        if ($groupId) {
            $belongs = auth()->user()
                ->groups()
                ->where('groups.id', $groupId)
                ->exists();

            abort_unless($belongs, 403, 'Não tens acesso a esse grupo.');
        }

        $imagePath = $pin->image_path;

        if ($request->boolean('remove_image')) {
            $this->deletePinImage($pin);
            $imagePath = null;
        }

        if ($request->hasFile('image')) {
            $this->deletePinImage($pin);
            $imagePath = $request->file('image')->store('pins', 'public');
        }

        $pin->update([
            'group_id'      => $groupId,
            'title'         => $data['title'],
            'content'       => $data['content'] ?? null,
            'location_text' => $data['location_text'] ?? null,
            'lat'           => $data['lat'] ?? null,
            'lng'           => $data['lng'] ?? null,
            'image_path'    => $imagePath,
        ]);

        return redirect()
            ->route('pins.show', $pin)
            ->with('status', 'Pin atualizado com sucesso!');
    }

    public function destroy(Pin $pin): RedirectResponse
    {
        $this->authorize('delete', $pin);

        $this->deletePinImage($pin);

        $pin->delete();

        return redirect()
            ->route('pins.index')
            ->with('status', 'Pin apagado com sucesso!');
    }

    private function normalizeGroupId($value): ?int
    {
        if ($value === null || $value === '' || $value === 'null') {
            return null;
        }

        return (int) $value;
    }

    private function deletePinImage(Pin $pin): void
    {
        if (!$pin->image_storage_path) {
            return;
        }

        if (Storage::disk('public')->exists($pin->image_storage_path)) {
            Storage::disk('public')->delete($pin->image_storage_path);
        }
    }
}