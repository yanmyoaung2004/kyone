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

        if ($order) {

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
        } else {

            return response()->json([
                'success' => false,
                'message' => 'No Order Found.'
            ], 404);
        }
    }
    public function onProgressOrders()
    {
        try {
            $orders = Order::where('status', 'inprogress')->get();
            return response()->json(['orders' => $orders], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Get on progress order failed', 'error' => $e->getMessage()], 500);
        }
    }
    public function filterOrders(Request $request)
    {
        // Get the filter parameters (status and location in this case)
        $filters = $request->query('filter', []);
    
        // Check if a valid status filter is provided
        $validStatuses = ['pending', 'inprogress', 'delayed', 'delivered', 'cancelled'];
    
        if (isset($filters['status']) && !empty($filters['status'])) {
            $statuses = explode(',', $filters['status']); // Split by commas if multiple statuses are provided
    
            // Validate each status in the array
            foreach ($statuses as $status) {
                if (!in_array($status, $validStatuses)) {
                    return response()->json(['message' => 'Invalid status provided'], 400);
                }
            }
        }
    
        // Check if a valid location filter is provided
        if (isset($filters['location']) && !empty($filters['location'])) {
            $locations = explode(',', $filters['location']); // Split by commas if multiple locations are provided
    
            // Validate each location in the array (ensure they are numeric)
            foreach ($locations as $location) {
                if (!is_numeric($location)) {
                    return response()->json(['message' => 'Invalid location ID provided'], 400);
                }
            }
        }
    
        // Build the query
        $query = Order::query();
    
        // Apply status filter (if provided)
        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->whereIn('status', $statuses);
        }
    
        // Apply location filter (if provided)
        if (isset($filters['location']) && !empty($filters['location'])) {
            $query->whereIn('location_id', $locations);
        }
    
        // Get the filtered orders
        $orders = $query->get();
    
        // Check if no orders were found
        if ($orders->isEmpty()) {
            return response()->json(['message' => 'No orders found for the given filter'], 404);
        }
    
        // Return the filtered orders
        return response()->json([
            'order_count' => $orders->count(),
            'orders' => $orders // You can specify fields to return if needed
        ]);
    }
    

    
    public function orderLocationFilter(Request $request)
    {
        // Retrieve the location filter from query parameters
        $locationId = $request->query('filter.location');
    
        if ($locationId) {
            // Fetch orders by location_id
            $orders = Order::where('location_id', $locationId)->get();
    
            // Return response
            return response()->json([
                'orders' => $orders
            ], 200);
        } else {
            return response()->json(['message' => 'No orders found for the given Location'], 404);

        }
    }
    
}
