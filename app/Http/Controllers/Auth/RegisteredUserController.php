<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar página de registo
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Processar registo de novo utilizador
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Criar utilizador
        |--------------------------------------------------------------------------
        */

        $user = User::create([
            'name' => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Disparar evento de registo
        |--------------------------------------------------------------------------
        | Isto envia automaticamente o email de verificação
        |--------------------------------------------------------------------------
        */

        event(new Registered($user));

        /*
        |--------------------------------------------------------------------------
        | Login automático após registo
        |--------------------------------------------------------------------------
        */

        Auth::login($user);

        /*
        |--------------------------------------------------------------------------
        | Redirecionar para dashboard
        |--------------------------------------------------------------------------
        | Como usas middleware verified,
        | Laravel vai automaticamente redirecionar
        | para verify-email se ainda não estiver verificado.
        |--------------------------------------------------------------------------
        */

        return redirect()->route('dashboard');
    }
}