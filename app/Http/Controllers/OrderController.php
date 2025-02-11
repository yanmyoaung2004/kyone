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
use App\Models\Customer;
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
        $customer = Customer::where('user_id', $userId)->first();
        $orders = Order::with('products')->where('customer_id', $customer->id)->get();

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
    }



    public function store(OrderStoreRequest $request)
    {
        DB::beginTransaction();
        try {
            $customer = Customer::where('user_id', $request->get('customer_id'))->first();
            $items = $request->get('items');
            $shipmentInfo = $request->get('shipmentInfo');
            $location = Location::create([
                'address' => $shipmentInfo['address'],
                'state' => $shipmentInfo['state'],
                'city_id' => $shipmentInfo['city'],
            ]);
            $order = Order::create([
                'customer_id' => $customer->id,
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
}
