<?php
namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BrandController extends Controller
{
    // Create a new Brand
    public function store(Request $request)
    {
        try{
        $validatedData = $request->validate([
            'name' => 'required|string|max:255'
        ]);
        $brand = Brand::create(['name' => $validatedData['name']]);
        return response()->json(['message' => 'Brand created successfully!', 'brand' => $brand], 201);

    } catch (ValidationException $e) {
        return response()->json([
            'message' => $e->errors(),    
        ], 422);
    }
    }

    // Get all Brands
    public function index()
    {
        $brands = Brand::all();

        return response()->json($brands);
    }

    // Get a single Brand by ID
    public function show($id)
    {
        $brand = Brand::findOrFail($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
        return response()->json($brand);
    }

    // Update a Brand
    public function update(Request $request, $id)
    {
        try{
         $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $brand = Brand::findOrFail($id);
        $brand->update($request->all());
        return response()->json(['message' => 'Brand updated successfully!', 'brand' => $brand]);

    }catch (ValidationException $e) {
        return response()->json([
            'message' =>  $e->errors(),        
        ], 422);
    }
    }

    // Delete a Brand
    public function destroy($id)
    {
        $brand = Brand::findOrFail($id);

        if (!$brand) {
            return response()->json(['message' => 'Brand not found'], 404);
        }
    
        $brand->delete();

        return response()->json(['message' => 'Brand deleted successfully!']);
    }
}
