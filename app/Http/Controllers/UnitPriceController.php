<?php
namespace App\Http\Controllers;

use App\Models\Unitprice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Response;

class UnitpriceController extends Controller
{
    /**
     * Display a listing of the Unitprices with caching.
     *
     * @return Response
     */
    public function index()
    {
        $cacheKey = 'unitprices_all';

        // Check if the cache is available and return it
        $unitprices = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return Unitprice::select('product_id', 'price')->get();
        });

        return response()->json($unitprices);
    }

    /**
     * Store a newly created Unitprice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        // Validate input data
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);

        // Create or update Unitprice
        $unitprice = Unitprice::createOrUpdate($validated);

        // Clear the cache as we added/updated a unitprice
        Cache::forget('unitprices_all');

        return response()->json($unitprice, 201);
    }

    /**
     * Display the specified Unitprice with caching.
     *
     * @param  int  $id
     * @return Response
     */
    public function show(int $id)
    {
        $cacheKey = "unitprice_{$id}";

        // Check if the cache is available and return it
        $unitprice = Cache::remember($cacheKey, now()->addMinutes(60), function () use ($id) {
            return Unitprice::find($id);
        });

        if (!$unitprice) {
            return response()->json(['message' => 'Unitprice not found'], 404);
        }

        return response()->json($unitprice);
    }

    /**
     * Update the specified Unitprice in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, int $id)
    {
        // Validate input data
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'price' => 'required|numeric|min:0',
        ]);

        $unitprice = Unitprice::find($id);

        if (!$unitprice) {
            return response()->json(['message' => 'Unitprice not found'], 404);
        }

        // Update Unitprice
        $unitprice->update($validated);

        // Clear cache for both all unit prices and the specific one
        Cache::forget('unitprices_all');
        Cache::forget("unitprice_{$id}");

        return response()->json($unitprice);
    }

    /**
     * Remove the specified Unitprice from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy(int $id)
    {
        $unitprice = Unitprice::find($id);

        if (!$unitprice) {
            return response()->json(['message' => 'Unitprice not found'], 404);
        }

        // Delete Unitprice
        $unitprice->delete();

        // Clear the cache for both all unit prices and the specific one
        Cache::forget('unitprices_all');
        Cache::forget("unitprice_{$id}");

        return response()->json(['message' => 'Unitprice deleted successfully']);
    }

    /**
     * Bulk insert Unitprices for better performance with caching.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function bulkInsert(Request $request)
    {
        // Validate the array of unitprices
        $validated = $request->validate([
            'unitprices' => 'required|array',
            'unitprices.*.product_id' => 'required|integer|exists:products,id',
            'unitprices.*.price' => 'required|numeric|min:0',
        ]);

        // Bulk insert Unitprices
        $inserted = Unitprice::bulkInsert($validated['unitprices']);

        // Clear the cache for all unit prices
        Cache::forget('unitprices_all');

        return response()->json(['message' => 'Unitprices inserted successfully'], 201);
    }
}
