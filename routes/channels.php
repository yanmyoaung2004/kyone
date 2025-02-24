<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
},['guards' => ['web','sanctum']]);
Broadcast::channel('public-updates', function ($user, $id) {
    return true;
});

Broadcast::channel('chat.{id}', function ($user, $id) { 
    return (int) $user->id === (int) $id;
},['guards' => ['web','sanctum']]);

Broadcast::channel('role.{role}', function ($user, $role) {
    // Log::info($user);
    // return $user->role === $role;
    return true;
},['guards' => ['web','sanctum']]);