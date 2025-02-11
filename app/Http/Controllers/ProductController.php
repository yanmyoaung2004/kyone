<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unitprice;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;  // Custom FormRequest for validation

class ProductController extends Controller
{
    // Create a new product
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        if ($request->hasFile('medias')) {
            foreach (request('medias') as $file) {
                $product
                    ->addMedia($file)
                    ->toMediaCollection('medias');
            }
        }

        return response()->json($product, 201);
    }

    // Show a single product
    public function show($id)
    {
        $product = Product::with(['category', 'unitprice', 'orders'])->find($id);

        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        $product->load('medias');
        // dd($product->medias);

        return response()->json($product);
    }

    // Update an existing product
    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);

        if ($request->hasFile('medias')) {
            $product->clearMediaCollection('medias');
            foreach ($request->file('medias') as $file) {
                $product
                    ->addMedia($file)
                    ->toMediaCollection('medias');
            }
        }

        $product->medias;

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
        $products = Product::with(['category', 'unitprice', 'medias'])->get();
        return response()->json($products);
    }


    public function filterProducts(Request $request)
    {
        // Get the filters from the query string
        $filters = $request->query();

        // Build the query to fetch products
        $query = Product::query();

        // Filter by brand names if provided
        if (isset($filters['filter']['brand.name'])) {
            $brandNames = explode(',', $filters['filter']['brand.name']);
            $query->whereHas('brand', function ($query) use ($brandNames) {
                $query->whereIn('name', $brandNames); // Filter by multiple brand names
            });
        }

        // Filter by category names if provided
        if (isset($filters['filter']['category.name'])) {
            $categoryNames = explode(',', $filters['filter']['category.name']);
            $query->whereHas('category', function ($query) use ($categoryNames) {
                $query->whereIn('name', $categoryNames); // Filter by multiple category names
            });
        }

        // Filter by price range if provided
        if (isset($filters['filter']['pricerange'])) {
            $priceRange = explode(',', $filters['filter']['pricerange']);
            if (count($priceRange) == 2) {
                $minPrice = $priceRange[0];
                $maxPrice = $priceRange[1];
                $query->whereHas('unitprice', function ($query) use ($minPrice, $maxPrice) {
                    $query->whereBetween('price', [$minPrice, $maxPrice]); // Filter by price range
                });
            }
        }

        // Get the filtered products
        $products = $query->get();

        // Check if no products were found
        if ($products->isEmpty()) {
            return response()->json([
                'message' => 'No products found for the given filter criteria.'
            ], 404); // Return a 404 response if no products are found
        }

        foreach ($products as $product) {
            $product->media;
        }

        // Return the filtered products as a JSON response
        return response()->json([
            'product_count' => $products->count(),
            'products' => $products
        ]);
    }
}
