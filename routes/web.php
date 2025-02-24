<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/users', function () {
    $users = User::with('roles')->get();
    return response()->json($users);
});

