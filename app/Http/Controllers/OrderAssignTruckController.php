<?php
namespace App\Http\Controllers;

use App\Events\UserEvent;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderAssignTruck;
use App\Models\Truck;
use Illuminate\Validation\ValidationException;

class OrderAssignTruckController extends Controller
{
    public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'exists:orders,id',
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
        ]);

        $assignedOrders = [];

        foreach ($validated['orders'] as $orderId) {
            $order = Order::find($orderId);
            $order->update(['status'=>'processing']);
            $assignedOrders[] = OrderAssignTruck::create([
                'order_id' => $orderId,
                'driver_id' => $validated['driver_id'],
                'truck_id' => $validated['truck_id'],
                ]);

            $driver = Driver::find($validated['driver_id']);
            $driver->update([
                    'status' => 'busy'
            ]);
            $notification = Notification::create([
                        'resource_id' => $order->customer->id,
                        'type' => 'order',
                        'role' => 'customer',
                        'message' => 'Your Order '. substr($order->invoice->invoice_number, 0, 9). ' has been dispatched!',
                    ]);
                broadcast(new UserEvent($notification))->toOthers();

                $notification = Notification::create([
                        'resource_id' => $order->customer->id,
                        'type' => 'order',
                        'role' => 'driver',
                        'message' => 'Order '. substr($order->customer->id, 0, 9). ' has been assigned to your truck!',
                    ]);
                broadcast(new UserEvent($notification))->toOthers();
        }

        return response()->json([
            'message' => 'Orders assigned successfully',
            'order_assign_trucks' => $assignedOrders
        ], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => $e->errors(),
        ], 422);
    }
}


    public function index()
    {
        return response()->json(OrderAssignTruck::all());
    }

    public function show($id)
    {
        return response()->json(OrderAssignTruck::findOrFail($id));
    }

    public function update(Request $request, OrderAssignTruck $orderAssignTruck)
    {
        try{
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'driver_id' => 'required|exists:drivers,id',
            'truck_id' => 'required|exists:trucks,id',
        ]);

        $orderAssignTruck->update($validated);

        return response()->json(['message' => 'Order assignment updated successfully', 'order_assign_truck' => $orderAssignTruck]);
    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),

        ], 422);
    }
    }

    public function destroy($id)
    {
        OrderAssignTruck::findOrFail($id)->delete();
        return response()->json(['message' => 'Order assignment deleted successfully']);
    }


    public function assignedOrder($id){
        $truck = OrderAssignTruck::where('truck_id',$id)->with('order')->get();

        return response()->json(['truck'=>$truck]);
    }
}
