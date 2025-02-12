<?php
namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\OrderAssignTruck;
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
