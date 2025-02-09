<?php
namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        return response()->json(Category::all());
    }

    // Store a new category
    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255|unique:categories',
            ], [
                'name.required' => 'The category name is required.',
                'name.unique' => 'This category name already exists.',
            ]);
    
            $category = Category::create(['name' => $validatedData['name']]);
    
            return response()->json([
                'message' => 'Category created successfully!',
                'category' => $category
            ], 201);
            
        } catch (ValidationException $e) {
            return response()->json([
                'message' => $e->errors(),
                
            ], 422);
        }
    }

    // Show a single category
    public function show($id)
    {
        $category = Category::findOrFail($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json($category);
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        try {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $id,
        ]);

        $category->update(['name' => $request->name]);

        return response()->json($category);
        
    }catch (ValidationException $e) {
        return response()->json([
            'message' =>  $e->errors(),
           
        ], 422);
    }
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}
