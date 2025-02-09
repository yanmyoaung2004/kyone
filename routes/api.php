<?php

use App\Http\Controllers\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/customers',[CustomerController::class,'create']);
Route::get('/customers',[CustomerController::class,'getAllCustomers']);
Route::get('/customers/{id}',[CustomerController::class,'getCustomer']);
Route::patch('/customers/{id}',[CustomerController::class,'update']);
Route::delete('/customers/{id}',[CustomerController::class,'delete']);
