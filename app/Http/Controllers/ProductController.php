<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;  // Custom FormRequest for validation

class ProductController extends Controller
{
    // Create a new product
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());

        return response()->json($product, 201);
    }

    // Show a single product
    public function show($id)
    {
        $product = Product::with(['category', 'unitprice', 'orders'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    // Update an existing product
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Use only the validated data for update
        $product->update($request->validated());

        return response()->json($product);
    }

    // Delete a product
    public function destroy($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    // List all products with category and unitprice eager-loaded
    public function index()
    {
        $products = Product::with(['category', 'unitprice'])->get();

        return response()->json($products);
    }
}
