<?php

use App\Events\UserEvent;
use App\Models\Notification;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/users', function () {
    $users = User::with('roles')->get();
    return response()->json($users);
});


// Route::get('/push', function () {
//     $notification = Notification::create([
//         'resource_id' => 1,
//         'type' => 'order',
//         'role' => 'customer',
//         'message' => 'Order done.'
//     ]);
//     broadcast(new UserEvent($notification));
//     return 1;
// });

