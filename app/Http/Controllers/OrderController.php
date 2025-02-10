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

    public function onProgressOrders()
    {
        try {
            $orders = Order::where('status', 'inprogress')->get();
            return response()->json(['orders' => $orders],200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Get on progress order failed', 'error' => $e->getMessage()],500);
        }
    }
}
