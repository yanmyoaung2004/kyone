<?php

namespace App\Http\Controllers;

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
}
