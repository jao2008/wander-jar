<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $user->update(['name' => $validated['name']]);

        return back()->with('status', 'Nome atualizado com sucesso! ✅');
    }

    public function updateEmail(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        $newEmail = $validated['email'];

        if ($newEmail === $user->email) {
            return back()->with('status', 'O email é o mesmo, não houve alterações.');
        }

        $user->email = $newEmail;
        $user->email_verified_at = null;
        $user->save();

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('status', 'Email atualizado com sucesso! Verifica o novo email para o confirmar. ✉️');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = auth()->user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update(['profile_photo' => $path]);

        return back()->with('status', 'Foto atualizada com sucesso! ✅');
    }

    public function deletePhoto()
    {
        $user = auth()->user();

        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo)) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update(['profile_photo' => null]);

        return back()->with('status', 'Foto removida com sucesso! ✅');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = auth()->user();
        $user->update(['password' => Hash::make($validated['password'])]);

        return back()->with('status', 'Password atualizada com sucesso! ✅');
    }

    /**
     * Serve a foto de perfil via Laravel (contorna o 403 do /storage em Apache)
     */
    public function photo(User $user)
    {
        if (auth()->id() !== $user->id) {
            abort(403);
        }

        if (!$user->profile_photo || !Storage::disk('public')->exists($user->profile_photo)) {
            abort(404);
        }

        $absolutePath = Storage::disk('public')->path($user->profile_photo);

        return response()->file($absolutePath);
    }
}