<?php
namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DriverController extends Controller
{
    // 1. CREATE Driver
    public function store(Request $request)
    {
        try{


        $request->validate([
            'user_id' => 'required|exists:users,id',
            'driver_license' => 'required|unique:drivers,driver_license',
            'nrc_number' => 'required|unique:drivers,nrc_number',
            'phone' => 'required|string',
        ]);

        $driver = Driver::create([
            'user_id' => $request->user_id,
            'driver_license' => $request->driver_license,
            'nrc_number' => $request->nrc_number,
            'phone' => $request->phone,
        ]);

        return response()->json(['message' => 'Driver created successfully', 'driver' => $driver]);
    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),

        ], 422);
    }
    }

    // 2. READ all drivers
    public function index()
    {
        $drivers = Driver::all();
        return response()->json($drivers);
    }

    // 3. READ a single driver
    public function show($id)
    {
        $driver = Driver::find($id);
        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }
        return response()->json($driver);
    }

    public function getFreeDriver(){
        $driver = Driver::with('user')->where('status', 'free')->get();
        return response()->json($driver);
    }

    // 4. UPDATE a driver
    public function update(Request $request, $id)
    {

        $driver = Driver::find($id);
        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }

        try{
        $request->validate([
            'user_id' => 'exists:users,id',
            'driver_license' => 'unique:drivers,driver_license,' . $id,
            'nrc_number' => 'unique:drivers,nrc_number,' . $id,
            'phone' => 'string',
        ]);

        $driver->update([
            'user_id' => $request->user_id ?? $driver->user_id,
            'driver_license' => $request->driver_license ?? $driver->driver_license,
            'nrc_number' => $request->nrc_number ?? $driver->nrc_number,
            'phone' => $request->phone ?? $driver->phone,
        ]);

        return response()->json(['message' => 'Driver updated successfully', 'driver' => $driver]);
    } catch (ValidationException $e) {
        return response()->json([
            'message'=> $e->errors(),

        ], 422);
    }
    }

    // 5. DELETE a driver
    public function destroy($id)
    {
        $driver = Driver::find($id);
        if (!$driver) {
            return response()->json(['message' => 'Driver not found'], 404);
        }
        $driver->delete();

        return response()->json(['message' => 'Driver deleted successfully']);
    }
}
