<?php

namespace App\Http\Controllers;

use App\Http\Requests\ServiceCenterRequest;
use App\Models\ServiceCenter;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ServiceCenterController extends Controller
{
    public function index()
    {
        $serviceCenters = ServiceCenter::all();
        return response()->json($serviceCenters);
    }

    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'location' => 'required|string|max:255',
                'phone' => 'required|string|max:20|unique:service_centers,phone'
            ]);

            $serviceCenter = ServiceCenter::create($validated);
            return response()->json(['message' => 'Service center created successfully!', 'data' => $serviceCenter], 201);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to create service center!'], 500);
        }
    }

    public function show($id)
    {
        $serviceCenter = ServiceCenter::find($id);
        if (!$serviceCenter) {
            return response()->json(['error' => 'Service center not found!'], 404);
        }
        return response()->json($serviceCenter);
    }

    public function update($id, Request $request)
    {
        $serviceCenter = ServiceCenter::find($id);
        if (!$serviceCenter) {
            return response()->json(['error' => 'Service center not found!'], 404);
        };

        try {
            $serviceCenter->update($request->all());
            return response()->json(['message' => 'Service center updated successfully!', 'data' => $serviceCenter]);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to update service center!'], 500);
        }
    }

    public function destroy($id)
    {
        $serviceCenter = ServiceCenter::find($id);
        if (!$serviceCenter) {
            return response()->json(['error' => 'Service center not found!'], 404);
        }

        try {
            $serviceCenter->delete();
            return response()->json(['message' => 'Service center deleted successfully!']);
        } catch (QueryException $e) {
            return response()->json(['error' => 'Failed to delete service center!'], 500);
        }
    }
}
