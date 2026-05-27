<?php

namespace App\Providers;

use App\Models\Event;
use App\Models\Group;
use App\Models\Pin;
use App\Policies\EventPolicy;
use App\Policies\GroupPolicy;
use App\Policies\PinPolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.url') . "/reset-password/{$token}?email=" . urlencode($notifiable->getEmailForPasswordReset());
        });

        VerifyEmail::createUrlUsing(function (object $notifiable) {
            return URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
        });

        Gate::policy(Group::class, GroupPolicy::class);
        Gate::policy(Pin::class, PinPolicy::class);
        Gate::policy(Event::class, EventPolicy::class);
    }
}