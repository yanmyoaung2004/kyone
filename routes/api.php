<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderAssignTruckController;
use App\Http\Controllers\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\CategoryController;
use App\Models\Driver;
use App\Models\OrderAssignTruck;

Route::apiResource('categories', CategoryController::class);
Route::apiResource('invoices',InvoiceController::class);
Route::apiResource('dirvers',DriverController::class);
Route::apiResource('trucks',TruckController::class);
Route::apiResource('complaints',ComplaintController::class);
Route::apiResource('orderAssignTrucks',OrderAssignTruckController::class);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

