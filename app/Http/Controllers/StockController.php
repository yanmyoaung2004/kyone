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
    public function index()
    {
        try {
            $stocks = Cache::remember('stocks', 60, function () {
                return Stock::with(['product', 'unitprice'])->get();
            });
            return response()->json($stocks);
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
}
