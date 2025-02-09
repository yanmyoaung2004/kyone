<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\CategoryController;
use App\Models\Driver;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('invoices',InvoiceController::class);
Route::apiResource('dirvers',DriverController::class);
Route::apiResource('trucks',TruckController::class);

