<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderCollection;
use App\Http\Requests\OrderStoreRequest;
use App\Http\Requests\OrderUpdateRequest;

class OrderController extends Controller
{
    public function index(Request $request): OrderCollection
    {
        $orders = Order::all();

        return new OrderCollection($orders);
    }

    public function store(OrderStoreRequest $request): OrderResource
    {
        $order = Order::create($request->validated());

        $order->payment()->create([
            'method' => $request->method,
            'amount' => $order->total_price,
            'status' => 'pending',
        ]);

        $order->invoice()->create([
            'total_amount' => $order->total_price,
            'invoice_number' => 'INV-' . Str::uuid(),
        ]);

        return new OrderResource($order);
    }

    public function show(Request $request, Order $order): OrderResource
    {
        return new OrderResource($order);
    }

    public function update(OrderUpdateRequest $request, Order $order): OrderResource
    {
        $order->update($request->validated());

        return new OrderResource($order);
    }

    public function destroy(Request $request, Order $order): Response
    {
        $order->delete();

        return response()->noContent();
    }
public function getTruckAndDriverByOrderId($id)
{
    
        // Find the order or throw a 404 error if not found
        $order = Order::findOrFail($id);

        if($order){

            // dd($order->orderAssignTruck);
              // Check if the order has an assigned truck and driver
        if (!$order->orderAssignTruck) {
            return response()->json([
                'success' => false,
                'message' => 'No truck or driver assigned to this order.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order' => $order,
            'truck' => $order->orderAssignTruck->truck,
            'driver' => $order->orderAssignTruck->driver,
        ], 200);
        
    } else{
       
            return response()->json([
                'success' => false,
                'message' => 'No Order Found.'
            ], 404);
        }
    } 
    }




