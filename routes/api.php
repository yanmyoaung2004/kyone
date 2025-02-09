<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitpriceController;

Route::apiResource('categories', CategoryController::class);

Route::apiResource('stocks', StockController::class);
Route::apiResource('unitprices', UnitpriceController::class);
Route::apiResource('products', ProductController::class);

