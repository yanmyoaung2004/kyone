<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitpriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::apiResource('orders', App\Http\Controllers\OrderController::class);
Route::post('/customers',[CustomerController::class,'create']);
Route::get('/customers',[CustomerController::class,'getAllCustomers']);
Route::get('/customers/{id}',[CustomerController::class,'getCustomer']);
Route::patch('/customers/{id}',[CustomerController::class,'update']);
Route::delete('/customers/{id}',[CustomerController::class,'delete']);


Route::apiResource('categories', CategoryController::class);
Route::apiResource('invoices',InvoiceController::class);
Route::apiResource('dirvers',DriverController::class);
Route::apiResource('trucks',TruckController::class);
Route::apiResource('complaints',ComplaintController::class);
Route::apiResource('stocks', StockController::class);
Route::get('/stocks/check_stock/{productId}',[StockController::class,'checkStock']);
Route::apiResource('unitprices', UnitpriceController::class);
Route::apiResource('products', ProductController::class);

