<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\GroupChatController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\GroupInviteController;
use App\Http\Controllers\PinController;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('home');
})->name('home');

require __DIR__ . '/auth.php';

Route::middleware(['auth'])->group(function () {

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/name', [ProfileController::class, 'updateName'])->name('update.name');
        Route::patch('/email', [ProfileController::class, 'updateEmail'])->name('update.email');
        Route::post('/photo', [ProfileController::class, 'updatePhoto'])->name('update.photo');
        Route::delete('/photo', [ProfileController::class, 'deletePhoto'])->name('delete.photo');
        Route::patch('/password', [ProfileController::class, 'updatePassword'])->name('update.password');

        Route::get('/photo/{user}', [ProfileController::class, 'photo'])->name('photo.show');
    });

});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/map', [PinController::class, 'map'])->name('map');

    /*
    |--------------------------------------------------------------------------
    | Pins
    |--------------------------------------------------------------------------
    */

    Route::get('/pins', [PinController::class, 'index'])->name('pins.index');
    Route::get('/pins/index', fn () => redirect()->route('pins.index'));

    Route::prefix('pins')->name('pins.')->group(function () {
        Route::get('/create', [PinController::class, 'create'])->name('create');
        Route::post('/', [PinController::class, 'store'])->name('store');

        Route::get('/{pin}/edit', [PinController::class, 'edit'])->name('edit');
        Route::patch('/{pin}', [PinController::class, 'update'])->name('update');

        Route::get('/{pin}', [PinController::class, 'show'])->name('show');
        Route::delete('/{pin}', [PinController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Groups
    |--------------------------------------------------------------------------
    */

    Route::prefix('groups')->name('groups.')->group(function () {
        Route::get('/', [GroupController::class, 'index'])->name('index');
        Route::get('/create', [GroupController::class, 'create'])->name('create');
        Route::post('/', [GroupController::class, 'store'])->name('store');

        Route::get('/join/{invite_code}', [GroupController::class, 'joinByInvite'])->name('join');

        Route::middleware('group.member')->group(function () {
            Route::get('/{group}/edit', [GroupController::class, 'edit'])->name('edit');
            Route::patch('/{group}', [GroupController::class, 'update'])->name('update');

            Route::get('/{group}/map', [GroupController::class, 'map'])->name('map');

            Route::get('/{group}/members', [GroupController::class, 'members'])->name('members');
            Route::patch('/{group}/members/{user}/promote', [GroupController::class, 'promoteMember'])->name('members.promote');
            Route::patch('/{group}/members/{user}/demote', [GroupController::class, 'demoteMember'])->name('members.demote');
            Route::delete('/{group}/members/{user}/remove', [GroupController::class, 'removeMember'])->name('members.remove');

            Route::get('/{group}/chat', [GroupChatController::class, 'show'])->name('chat');
            Route::post('/{group}/chat/messages', [GroupChatController::class, 'store'])->name('chat.store');
            Route::get('/{group}/chat/messages', [GroupChatController::class, 'fetch'])->name('chat.messages.fetch');
        });
    });

    Route::get('/invite/{code}', [GroupInviteController::class, 'accept'])
        ->name('groups.invite.accept');

    /*
    |--------------------------------------------------------------------------
    | Events
    |--------------------------------------------------------------------------
    */

    Route::prefix('events')->name('events.')->group(function () {
        Route::get('/', [EventController::class, 'index'])->name('index');
        Route::get('/create', [EventController::class, 'create'])->name('create');
        Route::post('/', [EventController::class, 'store'])->name('store');

        Route::get('/{event}/edit', [EventController::class, 'edit'])->name('edit');
        Route::patch('/{event}', [EventController::class, 'update'])->name('update');

        Route::get('/{event}', [EventController::class, 'show'])->name('show');

        Route::post('/{event}/join', [EventController::class, 'join'])->name('join');
        Route::post('/{event}/leave', [EventController::class, 'leave'])->name('leave');

        Route::patch('/{event}/cancel', [EventController::class, 'cancel'])->name('cancel');
        Route::delete('/{event}', [EventController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | Admin
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {

        Route::get('/', [AdminController::class, 'index'])->name('dashboard');

        /*
        |--------------------------------------------------------------------------
        | Utilizadores
        |--------------------------------------------------------------------------
        */

        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::post('/users/{user}/make-admin', [AdminController::class, 'makeAdmin'])->name('users.make-admin');
        Route::post('/users/{user}/remove-admin', [AdminController::class, 'removeAdmin'])->name('users.remove-admin');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        /*
        |--------------------------------------------------------------------------
        | Grupos
        |--------------------------------------------------------------------------
        */

        Route::get('/groups', [AdminController::class, 'groups'])->name('groups');
        Route::delete('/groups/{group}', [AdminController::class, 'destroyGroup'])->name('groups.destroy');

        /*
        |--------------------------------------------------------------------------
        | Pins
        |--------------------------------------------------------------------------
        */

        Route::get('/pins', [AdminController::class, 'pins'])->name('pins');
        Route::delete('/pins/{pin}', [AdminController::class, 'destroyPin'])->name('pins.destroy');

        /*
        |--------------------------------------------------------------------------
        | Eventos
        |--------------------------------------------------------------------------
        */

        Route::get('/events', [AdminController::class, 'events'])->name('events');
        Route::patch('/events/{event}/cancel', [AdminController::class, 'cancelEvent'])->name('events.cancel');
        Route::delete('/events/{event}', [AdminController::class, 'destroyEvent'])->name('events.destroy');

    });

});