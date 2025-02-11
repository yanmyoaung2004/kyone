<?php

namespace App\Http\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CityController extends Controller
{
    // Create a new City
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255'
            ]);

            $city = City::create(['name' => $validatedData['name']]);

            return response()->json([
                'message' => 'City created successfully!',
                'id' => $city->id,
                'name' => $city->name,
                'created_at' => $city->created_at
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
            ], 422);
        }
    }

    // Get all Cities
    public function index()
    {
        $cities = City::all();
        return response()->json($cities);
    }

    // Get a single City by ID
    public function show($id)
    {
        $city = City::findOrFail($id);

        return response()->json([
            'id' => $city->id,
            'name' => $city->name,
            'created_at' => $city->created_at
        ]);
    }

    // Update a City
    public function update(Request $request, $id)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $city = City::findOrFail($id);
            $city->update($validatedData);

            return response()->json([
                'message' => 'City updated successfully!',
                'id' => $city->id,
                'name' => $city->name,
                'created_at' => $city->created_at
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
            ], 422);
        }
    }

    // Delete a City
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return response()->json(['message' => 'City deleted successfully!']);
    }
}
