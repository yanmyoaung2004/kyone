<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderAssignTruckController;
use App\Http\Controllers\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\EscalatedIssueController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\UnitpriceController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::apiResource('orders', OrderController::class);

//Customer
Route::post('/customers',[CustomerController::class,'create']);
Route::get('/customers',[CustomerController::class,'getAllCustomers']);
Route::get('/customers/{id}/histories',[CustomerController::class,'histories']);
Route::get('/customers/{id}',[CustomerController::class,'getCustomer']);
Route::patch('/customers/{id}',[CustomerController::class,'update']);
Route::delete('/customers/{id}',[CustomerController::class,'delete']);

Route::apiResource('categories', CategoryController::class);
Route::apiResource('brands',BrandController::class);
Route::apiResource('invoices',InvoiceController::class);
Route::get('/customers/{customerId}/invoices',[InvoiceController::class,'customerInvoices']);
Route::apiResource('dirvers',DriverController::class);
Route::apiResource('trucks',TruckController::class);
Route::apiResource('complaints',ComplaintController::class);
Route::apiResource('stocks', StockController::class);
Route::apiResource('unitprices', UnitpriceController::class);
Route::apiResource('products', ProductController::class);
Route::apiResource('orderAssignTrucks',OrderAssignTruckController::class);


Route::get('/stocks/check_stock/{productId}',[StockController::class,'checkStock']);

//Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
// });

Route::get('/truck_assgiend_order/{id}',[OrderAssignTruckController::class,'assignedOrder']);
Route::get('/orders/on_progress',[OrderController::class,'onProgressOrders']);


Route::apiResource('escalated-issues', EscalatedIssueController::class);

Route::get('/sales/order_count_by_year/{year}',[SaleController::class,'orderCountByYear']);
Route::get('/sales/order_count_by_week/{week}',[SaleController::class,'orderCountByWeek']);
Route::get('/sales/order_count_by_day/{day}',[SaleController::class,'orderCountByDay']);

Route::get('/warehouse/low_stocks/{top}',[StockController::class,'lowStock']);
Route::get('/warehouse/stock_count_by_category/{categoryId}',[StockController::class,'checkStock']);
Route::get('/warehouse/free_and_assigned_trucks',[TruckController::class,'freeAndAssignedTrucks']);
//getDriverAndTruckByOrderID
Route::get('/orders/{id}/truck-driver', [OrderController::class, 'getTruckAndDriverByOrderId']);

//getTruckOrder
Route::get('/truck/{id}/orders', [TruckController::class, 'getTruckOrders']);
Route::get('orders/on_progress',[OrderController::class,'onProgressOrders']);


Route::apiResource('escalated-issues', App\Http\Controllers\EscalatedIssueController::class);

//Order filter
Route::get('/orders', [OrderController::class, 'filterOrders']);

//product filter
Route::get('/products', [ProductController::class, 'filterProducts']);//Complaint filter
Route::get('/complaints', [ComplaintController::class, 'filterComplaints']);

//Truck filter
Route::get('/tucks', [TruckController::class, 'filter']);

