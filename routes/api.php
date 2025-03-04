<?php

use App\Http\Controllers\CityController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\EscalatedIssueController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\OrderAssignTruckController;
use App\Http\Controllers\TruckController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\StockController;

use App\Http\Controllers\UnitpriceController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderReturnController;
use App\Http\Controllers\ServiceCenterController;

// This route will be public (no auth required)
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::apiResource('stocks', StockController::class)->only(['index', 'show']);
Route::apiResource('products', ProductController::class)->only(['index', 'show']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('orders', OrderController::class);

    //Customer
    Route::post('/customers', [CustomerController::class, 'create']);
    Route::get('/customers', [CustomerController::class, 'getAllCustomers']);
    Route::get('/customers/{id}/histories', [CustomerController::class, 'histories']);
    Route::get('/customers/{id}', [CustomerController::class, 'getCustomer']);
    Route::patch('/customers/{id}', [CustomerController::class, 'update']);
    Route::delete('/customers/{id}', [CustomerController::class, 'delete']);

    Route::apiResource('cities', CityController::class);
    Route::post('/cities/update/{id}', [CityController::class, 'update']);

    Route::apiResource('brands', BrandController::class);
    Route::apiResource('invoices', InvoiceController::class);
    Route::get('/customers/{customerId}/invoices', [InvoiceController::class, 'customerInvoices']);
    Route::apiResource('dirvers', DriverController::class);
    Route::apiResource('trucks', TruckController::class);
    Route::apiResource('complaints', ComplaintController::class);

    Route::apiResource('stocks', StockController::class)->except(['index', 'show']);


    Route::apiResource('unitprices', UnitpriceController::class);
    Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    Route::post('/products/{id}', [ProductController::class, 'update']);
    Route::apiResource('orderAssignTrucks', OrderAssignTruckController::class);


    Route::apiResource('/orders', App\Http\Controllers\OrderController::class);
    Route::get('/orders/user/{userId}', [OrderController::class, 'getOrderByUserId']);
    Route::get('/stocks/check_stock/{productId}', [StockController::class, 'checkStock']);


    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('/locations', LocationController::class);


    // Route::middleware('auth:sanctum')->group(function () {
    //     Route::post('/logout', [AuthController::class, 'logout']);
    // });

    Route::get('/truck_assgiend_order/{id}', [OrderAssignTruckController::class, 'assignedOrder']);
    Route::get('/orders/on_progress', [OrderController::class, 'onProgressOrders']);



    Route::get('/sales/order_count_by_year/{year}', [SaleController::class, 'orderCountByYear']);
    Route::get('/sales/order_count_by_week/{week}', [SaleController::class, 'orderCountByWeek']);
    Route::get('/sales/order_count_by_day/{day}', [SaleController::class, 'orderCountByDay']);

    Route::get('/warehouse/low_stocks/{top}', [StockController::class, 'lowStock']);
    Route::get('/warehouse/stock_count_by_category/{categoryId}', [StockController::class, 'checkStock']);
    Route::get('/warehouse/free_and_assigned_trucks', [TruckController::class, 'freeAndAssignedTrucks']);
    //getDriverAndTruckByOrderID
    Route::get('/orders/{id}/truck-driver', [OrderController::class, 'getTruckAndDriverByOrderId']);

    //getTruckOrder
    Route::get('/truck/{id}/orders', [TruckController::class, 'getTruckOrders']);
    Route::post('orders/{id}/status', [OrderController::class, 'changeStatus']);
    Route::get('orders/on_progress', [OrderController::class, 'onProgressOrders']);


    Route::apiResource('escalated-issues', EscalatedIssueController::class);
    Route::put('escalated-issues/{id}/update', [EscalatedIssueController::class, 'updateStatus']);

    Route::get('/deliveries', [DeliveryController::class, 'index']);
    Route::get('/deliveries/data', [DeliveryController::class, 'getData']);
    Route::get('/deliveries/{truckId}', [TruckController::class, 'getTruckOrders']);

    Route::get('warehouse/stocks', [StockController::class, 'indexForWarehouse']);
    Route::post('warehouse/stocks/update', [StockController::class, 'updateStock']);

    Route::get('sale/products/topSellingProducts/{year}', [ProductController::class, 'topSellingProducts']);
    Route::get('sale/products/getMonthlyOrders', [SaleController::class, 'getMonthlyOrdersMysql']);
    Route::get('sale/products/topSellingLocations/{i}', [SaleController::class, 'topSellingLocations']);


    //Truck filter
    Route::get('/trucks', action: [TruckController::class, 'filterTrucks']);
    Route::get('/drivers/getfree', action: [DriverController::class, 'getFreeDriver']);
    Route::apiResource('/drivers', DriverController::class);



    Route::get('/orders/accept/{orderId}', [OrderController::class, 'acceptOrder']);
    Route::get('/orders/getorderbyid/{orderId}', [OrderController::class, 'getOrderById']);


    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications', [NotificationController::class, 'create']);

    Route::apiResource('/returns', OrderReturnController::class);


    Route::post('/messages', [MessageController::class, 'create']);
    Route::get('/messages/sale/{receiver_id}', [MessageController::class, 'saleMessage']);
    Route::get('/messages/{customer_id}', [MessageController::class, 'customerMessage']);

    Route::post('/orders/create/return', [OrderController::class, 'createReturn']);

    Route::get('/warehouse/orders/data', [OrderController::class, 'getWarehouseData']);

    Route::get('warehouse/getproducts/{orderId}', [OrderController::class, 'getWarehouseProductData']);
    Route::get('warehouse/getReturns/data', [OrderController::class, 'getReturn']);
    Route::resource('service-centers', ServiceCenterController::class);

    // Protected category routes (create, update, delete)
    Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
});
