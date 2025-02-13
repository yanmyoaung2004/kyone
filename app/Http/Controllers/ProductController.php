<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ProductRequest;  // Custom FormRequest for validation
use App\Models\Stock;
use App\Models\Unitprice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required',
            'category_id' => 'required',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $product = Product::create([
            'name' => $request->name,
            'category_id' => $request->category_id,
            'description' => $request->description,
        ]);

        Unitprice::create([
            'product_id' => $product->id,
            'price' => $request->price,
        ]);


        if ($request->hasFile('image')) {
            $product->addMedia($request->file('image'))->toMediaCollection('products');
        }

        Stock::create([
            'product_id' => $product->id,
            'quantity' => 0,
            'safety_stock' => 0
        ]);

        $returnProductData = Product::with('unitprice', 'category')->find($product->id);

        return response()->json(['product' => $returnProductData->load('media')]);
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


    public function update(ProductRequest $request, $id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }



        if ($request->hasFile('image')) {
            $product->clearMediaCollection('medias');
            foreach ($request->file('image') as $file) {
                $product
                    ->addMedia($file)
                    ->toMediaCollection('products');
            }
        }
        $product->medias;
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
        $formattedData = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category->name,
                'description' => $product->description,
                'price' => $product->unitprice->price,
                'image' => $product->getImageUrlAttribute()
                ];

        });
        return response()->json($formattedData);
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

    public function topSellingProducts($year)
    {
        $topProducts = Product::select(
            'categories.name as category',
            'products.name as product',
            DB::raw('SUM(order_product.quantity) * 1 as orders')
        )
        ->join('order_product', 'products.id', '=', 'order_product.product_id')
        ->join('orders', 'order_product.order_id', '=', 'orders.id')
        ->join('categories', 'products.category_id', '=', 'categories.id')
        ->whereNotIn('orders.status', ['pending', 'cancelled'])
        ->groupBy('products.id', 'products.name', 'products.category_id', 'categories.name')
        ->orderByDesc('orders')
        ->take(5)
        ->get();

        $topProducts->transform(function ($product) {
            $product->orders = (int) $product->orders;
            return $product;
        });
        return response()->json($topProducts);

    }
}
