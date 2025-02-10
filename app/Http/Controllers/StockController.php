<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    try {
        $query = Stock::with(['product.category', 'unitprice']);

        // Apply search filter if 'search' parameter is present
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->whereHas('product', function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Apply category filter if 'category' parameter is present
        if ($request->has('category')) {
            $category = $request->input('category');
            $query->whereHas('product.category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Paginate the results
        $stocks = $query->paginate(10);

        // Transform the response
        $formattedStocks = collect($stocks->items())->map(function ($stock) {
            return [
                'id' => $stock->product->id,
                'name' => $stock->product->name,
                'description' => $stock->product->description,
                'price' => (float) $stock->unitprice->price,
                'image' => $stock->product->image ?? 'https://via.placeholder.com/150',
                'category' => $stock->product->category->name,
            ];
        });

        return response()->json([
            'data' => $formattedStocks,
            'pagination' => [
                'current_page' => $stocks->currentPage(),
                'last_page' => $stocks->lastPage(),
                'per_page' => $stocks->perPage(),
                'total' => $stocks->total(),
                'next_page_url' => $stocks->nextPageUrl(),
                'prev_page_url' => $stocks->previousPageUrl(),
            ],
        ]);
    } catch (Exception $e) {
        return response()->json(['error' => 'Failed to fetch stocks', 'message' => $e->getMessage()], 500);
    }
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:0',
                'safety_stock' => 'required|integer|min:0',
                'unitprice_id' => 'required|exists:unitprices,id',
            ]);

            $stock = Stock::create($request->all());
            Cache::forget('stocks');

            return response()->json($stock, 201);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to create stock', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $stock = Stock::with('product')->findOrFail($id);
            return response()->json($stock);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Stock not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to fetch stock', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $stock = Stock::findOrFail($id);

            $request->validate([
                'quantity' => 'sometimes|required|integer|min:0',
                'safety_stock' => 'sometimes|required|integer|min:0',
                'unitprice_id' => 'sometimes|required|exists:unitprices,id',
            ]);

            $stock->update($request->all());
            Cache::forget('stocks');

            return response()->json($stock);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Stock not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update stock', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $stock = Stock::findOrFail($id);
            $stock->delete();
            Cache::forget('stocks');

            return response()->json(['message' => 'Stock deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Stock not found'], 404);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to delete stock', 'message' => $e->getMessage()], 500);
        }
    }

    public function checkStock($productId)
    {
        try {
            $stockCount = Stock::where('product_id', $productId)->count();
            return response()->json(['stock_count' => $stockCount]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to check stock', 'message' => $e->getMessage()], 500);
        }
    }
}
