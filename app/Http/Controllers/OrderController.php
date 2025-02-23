<?php

namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\OrderResource;
use App\Http\Requests\OrderStoreRequest;
use App\Mail\OrderStatusUpdated;
use App\Models\Customer;
use App\Models\Location;
use App\Models\Notification;
use App\Models\Unitprice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{


    public function changeStatus(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);
        $newStatus = $request->input('status');

        // Validate the status
        $allowedStatuses = ['pending', 'processing', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedStatuses)) {
            return response()->json(['message' => 'Invalid status'], 400);
        }

        $order->status = $newStatus;
        $order->save();

        if($newStatus == 'completed'){
            $notification = Notification::create([
                    'resource_id' => $order->customer->id,
                    'type' => 'order',
                    'role' => 'customer',
                    'message' => 'Your Order '. substr($order->invoice->invoice_number, 0, 9). ' has been delivered!',
            ]);
            broadcast(new UserEvent($notification))->toOthers();

            $notificationSale = Notification::create([
                    'resource_id' => $order->customer->id,
                    'type' => 'order',
                    'role' => 'sale',
                    'message' => substr($order->invoice->invoice_number, 0, 9). ' has been delivered!',
            ]);
            broadcast(new UserEvent($notificationSale))->toOthers();
        }

        return response()->json(['message' => 'Order status updated successfully', 'status' => $newStatus]);
    }


    public function getWarehouseData()
    {
        $orders = Order::with(['customer.user', 'location'])
            ->where('status', 'processing')
            ->whereDoesntHave('orderAssignTruck')
            ->get();

        $formattedData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer' => $order->customer->user->name ?? 'Unknown',
                'address' => $order->location->address ?? 'No address',
                'city' => $order->location->city ?? "No City",
                'eta' => $order->location->city->eta,
                'status' => 'pending',
            ];
        });

        return response()->json($formattedData);
    }

    public function getWarehouseProductData($orderId)
    {
        $orders = Order::with(['products'])->where('id', $orderId)->first();
        return response()->json($orders->products);
    }

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
                'eta' => $order->location->city->eta,
                'status' => 'pending',
            ];
        });

        return response()->json($formattedData);
    }

    public function getReturn()
    {
        $orders = Order::with(['customer.user', 'location'])
            ->where('isReturn', true)
            ->get();
        $formattedData = $orders->map(function ($order) {
            return [
                'id' => $order->id,
                'customer' => $order->customer->user->name ?? 'Unknown',
                'address' => $order->location->address ?? 'No address',
                'eta' => $order->location->city->eta,
                'status' => 'pending',
            ];
        });

        return response()->json($formattedData);
    }


    public function getOrderByUserId($userId)
    {
        $customer = Customer::where('user_id', $userId)->first();
        $orders = Order::with('products', 'location')->where('customer_id', $customer->id)->get();
        $filteredOrder = $orders->map(function ($order) {
            return [
                'location' => $order->location,
                'invoiceId' => $order->id,
                'totalAmount' => $order->total_price,
                'buyDate' => $order->created_at,
                'status' => $order->status,
                'product' => $order->products->map(function ($p) {
                    $unitprice = Unitprice::find($p->pivot->unitprice_id);
                    return [
                        'productName' => $p->name,
                        'quantity' => $p->pivot->quantity,
                        'totalAmount' => $unitprice->price,
                    ];
                })
            ];
        });

        return response()->json($filteredOrder);
    }

    public function createReturn(Request $request)
    {
        DB::beginTransaction();
        try {
            $originalOrder = Order::findOrFail($request->get('order_id'));
            $items = $request->get('products');
            $returnOrder = Order::create([
                'customer_id' => $originalOrder->customer_id,
                'location_id' => $originalOrder->location_id,
                'status' => 'pending',
                'isReturn' => true,
                'return_id' => $originalOrder->id,
                'total_price' => 0,
                'eta' => 10,
            ]);
            $totalAmount = 0;
            foreach ($items as $item) {
                $pivotData = $originalOrder->products()->where('product_id', $item['id'])->first();
                if (!$pivotData || !$pivotData->pivot) {
                    throw new \Exception("Product not found in the original order.");
                }
                $unitPriceId = $pivotData->pivot->unitprice_id;
                $unitPrice = UnitPrice::find($unitPriceId)->price ?? 0;
                $subtotal = $unitPrice * $item['quantity'];
                $totalAmount += $subtotal;
                $returnOrder->products()->attach($item['id'], [
                    'unitprice_id' => $unitPriceId,
                    'quantity' => $item['quantity'],
                ]);
            }
            $returnOrder->update(['total_price' => $totalAmount]);
            $returnOrder->invoice()->create([
                'total_amount' => $totalAmount,
                'invoice_number' => 'INV-' . Str::uuid(),
            ]);
            DB::commit();
            return response()->json(['message' => 'Your return order has been successfully created!'], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Failed to create the return order',
                'message' => $e->getMessage(),
            ], 500);
        }
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
                'city_id' => $shipmentInfo['city'],
                'state' => 'test',
            ]);
            $order = Order::create([
                'customer_id' => $customer->id,
                'location_id' => $location->id,
                'status' => 'pending',
                'total_price' => $request->get('total'),
                'eta' => $location->city->eta,
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

            $notification = Notification::create([
                'resource_id' => $order->id,
                'type' => 'order',
                'role' => 'sale',
                'message' => $customer->user->name . ' has made an order!'
            ]);

            // Broadcast the event
            broadcast(new UserEvent($notification))->toOthers();

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


    public function acceptOrder($orderId)
    {
        DB::beginTransaction();
        try {
            $order = Order::with('products')->findOrFail($orderId);
            $order->update(['status' => 'processing']);
            foreach ($order->products as $product) {
                $stock = $product->stock;
                if ($stock) {
                    $stock->decrement('quantity', $product->pivot->quantity);
                }
            }
            DB::commit();

            Mail::to($order->customer->user->email)->send(new OrderStatusUpdated($order, 'processing'));

            $notification = Notification::create([
                'resource_id' => $order->customer->id,
                'type' => 'order',
                'role' => 'customer',
                'message' => 'Your Order '. substr($order->invoice->invoice_number, 0, 9). ' has been accepted!',
            ]);
            broadcast(new UserEvent($notification))->toOthers();

            $notificationWarehouse = Notification::create([
                'resource_id' => $order->id,
                'type' => 'order',
                'role' => 'warehouse',
                'message' => substr($order->invoice->invoice_number, 0, 9). ' requires to be dispatched!'
            ]);
            // Broadcast the event
            broadcast(new UserEvent($notificationWarehouse))->toOthers();


            return response()->json([
                'message' => 'Order successfully accepted!',
                'order_id' => $order->id,
                'status' => $order->status,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while accepting the order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getOrderById($orderId)
    {
        $order = Order::with('products.unitprice', 'customer.user')->find($orderId);
        return response()->json($order);
    }

    public function show( Order $order): OrderResource
    {
        return new OrderResource($order);
    }


    public function destroy(Request $request, Order $order): Response
    {
        $order->delete();

        return response()->noContent();
    }
}
