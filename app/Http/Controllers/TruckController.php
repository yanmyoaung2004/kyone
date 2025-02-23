<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderAssignTruck;
use App\Models\Truck;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TruckController extends Controller
{
    // 1. CREATE Truck
    public function store(Request $request)
    {
        try{

        $request->validate([
            'license_plate' => 'required|unique:trucks,license_plate|string|max:255',
        ]);

        $truck = Truck::create([
            'license_plate' => $request->license_plate,
        ]);

        return response()->json(['message' => 'Truck created successfully', 'truck' => $truck]);
    }catch (ValidationException $e) {
        return response()->json([
            'message' =>  $e->errors(),

        ], 422);
    }
    }

    // 2. READ all trucks
    public function index()
    {
        $trucks = Truck::all();
        return response()->json($trucks);
    }

    // 3. READ a single truck
    public function show($id)
    {
        $truck = Truck::find($id);
        if (!$truck) {
            return response()->json(['message' => 'Truck not found'], 404);
        }
        return response()->json($truck);
    }

    // 4. UPDATE a truck
    public function update(Request $request, $id)
    {
        $truck = Truck::find($id);
        if (!$truck) {
            return response()->json(['message' => 'Truck not found'], 404);
        }

        try{

        $request->validate([
            'license_plate' => 'required|unique:trucks,license_plate,' . $id . '|string|max:255',
        ]);

        $truck->update([
            'license_plate' => $request->license_plate ?? $truck->license_plate,
            'status' => $request->status,
        ]);
        return response()->json(['message' => 'Truck updated successfully', 'truck' => $truck]);
    }catch (ValidationException $e) {
        return response()->json([
            'message' =>  $e->errors(),
        ], 422);
    }
    }

    // 5. DELETE a truck
    public function destroy($id)
    {
        $truck = Truck::find($id);
        if (!$truck) {
            return response()->json(['message' => 'Truck not found'], 404);
        }
        $truck->delete();

        return response()->json(['message' => 'Truck deleted successfully']);
    }

    public function freeAndAssignedTrucks()
    {
        try {
            $freeTrucks = Truck::where('status', 'free')->get();
            $busyTrucks = Truck::where('status', 'busy')->get();
            return response()->json(['freeTrucks' => $freeTrucks, 'busyTrucks' => $busyTrucks], 200);
        } catch (Exception $e) {
            return response()->json(['error' => "Failed to get free and assigned trucks!", 'message' => $e->getMessage()], 500);
        }
    }

public function getTruckOrders($truckId)
{
    // Get orders assigned to this truck
    $assignedOrders = OrderAssignTruck::where('truck_id', $truckId)
        ->with(['order.customer.user', 'order.products', 'order.location' ,'driver.user'])
        ->get();

    $truck = Truck::find($truckId);
    if ($assignedOrders->isEmpty()) {
        return response()->json(['message' => 'No orders found for this truck'], 404);
    }

    return response()->json([
        'truck' => $truck,
        'order_count' => $assignedOrders->count(),
        'orders' => $assignedOrders->pluck('order'),
        'driver' => $assignedOrders->first()->driver
    ]);
}




public function filterTrucks(Request $request)
{
    // Get the filter parameters (status in this case)
    $filters = $request->query('filter', []);

    // Check if a valid status filter is provided
    $validStatuses =["free","busy"];

    if (isset($filters['status']) && !in_array($filters['status'], $validStatuses)) {
        return response()->json(['message' => 'Invalid status provided'], 400);
    }
    // Build the query
    $query = Truck::query();

    // Apply filters
    if (isset($filters['status'])) {
        $query->where('status', $filters['status']);
    }

    // Get the filtered orders
    $trucks = $query->get();

    // Check if no orders were found
    if ($trucks->isEmpty()) {
        return response()->json(['message' => 'No Complaint found for the given filter'], 404);
    }
    // Return the filtered orders
    return response()->json([
        'truck_count' => $trucks->count(),
        'trucks' => $trucks// You can specify fields to return if needed
    ]);
}
}
