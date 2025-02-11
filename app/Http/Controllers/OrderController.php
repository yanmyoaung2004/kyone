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
use App\Models\Location;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['customer.user', 'location'])
            ->where('status', 'pending')
            ->get();
        $formattedData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer' => $order->customer->user->name ?? 'Unknown',
                'address' => $order->location->address ?? 'No address',
                'eta' => 10,
                'status' => 'pending',
            ];
        });

        return response()->json($formattedData);
    }


    public function getOrderByUserId($userId)
    {
        $orders = Order::with('products')->where('customer_id', $userId)->get();

        $filteredOrder = $orders->map(function ($order) {
            return [
                'invoiceId' => $order->id,
                'totalAmount' => $order->total_price,
                'buyDate' => $order->created_at,
                'status' => $order->status,
                'product' => $order->products->map(function ($p) {
                    return [
                        'productName' => $p->name,
                        'quantity' => $p->pivot->quantity,
                        'totalAmount' => 'ksdfj',
                        ];
                    })
                ];
        });

        return response()->json($filteredOrder);



        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $orderProducts = $order->products;



        $data = $orderProducts->map(function ($product) {
            $pivotData = $product->pivot;
            return response()->json($pivotData);
            return [
                'product_id' => $product->id,
                'unitprice_id' => $pivotData->unitprice_id,
                'quantity' => $pivotData->quantity,
                ];
            });


        return response()->json($data);
    }



    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $items = $request->get('items');
            $shipmentInfo = $request->get('shipmentInfo');
            $location = Location::create([
                'address' => $shipmentInfo['address'],
                'state' => $shipmentInfo['state'],
                'city' => $shipmentInfo['city'],
            ]);
            $order = Order::create([
                'customer_id' => $request->get('customer_id'),
                'location_id' => $location->id,
                'status' => 'pending',
                'total_price' => $request->get('total'),
                'eta' => 10,
            ]);

            $order->payment()->create([
                'method' => $request->get('payment'),
                'amount' => $request->get('total'),
                'status' => 'pending',
            ]);

            $order->invoice()->create([
                'total_amount' => $request->get('total'),
                'invoice_number' => 'INV-' . Str::uuid(),
            ]);

            foreach ($items as $item) {
                $order->products()->attach($item['id'], [
                    'unitprice_id' => $item['unitprice_id'],
                    'quantity' => $item['quantity'],
                ]);
            }
            DB::commit();

            return response()->json(['message' => 'Your order has been successfully created!'], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Return error response with the exception message
            return response()->json([
                'error' => 'Failed to create the order',
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    public function show(Request $request, Order $order): OrderResource
    {
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

        $orders->load('orderAssignTruck.truck','orderAssignTruck.driver');
    
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
    
    
}
