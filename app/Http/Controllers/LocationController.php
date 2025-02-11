<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    // Display a listing of locations
    public function index()
    {
        $locations = Location::all();
        return response()->json($locations);
    }

    // Show the form for creating a new location
    public function create()
    {
        // Return a view or just a response for creating a location (depends on your front-end setup)
    }

    // Store a newly created location in storage
    public function store(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        $location = Location::create([
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
        ]);

        return response()->json($location, 201); // Created response
    }

    // Display the specified location
    public function show($id)
    {
        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    // Show the form for editing the specified location
    public function edit($id)
    {
        $location = Location::findOrFail($id);
        return response()->json($location);
    }

    // Update the specified location in storage
    public function update(Request $request, $id)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'city' => 'required|string|max:255',
        ]);

        $location = Location::findOrFail($id);
        $location->update([
            'address' => $request->address,
            'state' => $request->state,
            'city' => $request->city,
        ]);

        return response()->json($location);
    }

    // Remove the specified location from storage
    public function destroy($id)
    {
        $location = Location::findOrFail($id);
        $location->delete();

        return response()->json(['message' => 'Location deleted successfully']);
    }
}
