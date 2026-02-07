<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channel for withdraw success notifications
Broadcast::channel('withdraw-success', function () {
    return true; // Allow anyone to listen to public withdraw notifications
});

// Global notifications channel
Broadcast::channel('notifications.global', function () {
    return true; // Allow anyone to listen to global notifications
});

// Private user notifications channel
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

// Private notifications channel for authenticated users
Broadcast::channel('notifications.private', function ($user) {
    return auth()->check();
});

// Chat presence channel for active members tracking
Broadcast::channel('chat', function ($user) {
    return [
        'id' => $user->id,
        'name' => $user->name ?? $user->username,
        'joined_at' => now()->toISOString()
    ];
});
