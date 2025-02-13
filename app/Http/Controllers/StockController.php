<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Stock;
use App\Models\Unitprice;
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
        $stocks = Stock::with(['product.category', 'unitprice'])->get();
        $formattedData = $stocks->map(function ($stock) {
            $unitprice = Unitprice::where('product_id', $stock->product->id)->latest()->first();
            $stock = Stock::where('product_id', $stock->product->id)->first();
            $orderedQuantity = Order::where('status', 'pending')
                ->whereHas('products', function ($query) use ($stock) {
                    $query->where('product_id', $stock->product->id);
                })
                ->with('products')
                ->get()
                ->flatMap(function ($order) {
                    return $order->products->pluck('pivot.quantity');
                })
                ->sum();
            $remainingQuantity = $stock->quantity - $orderedQuantity;

            return [
                'available' => $remainingQuantity,
                'id' => $stock->product->id,
                'name' => $stock->product->name,
                'description' => $stock->product->description,
                'price_id' => $unitprice->id,
                'price' => (float) $unitprice->price,
                'image' => $stock->product->getImageUrlAttribute() ?? 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQinI_44p5jN05YioLyPBhn_1j5tsl7q85rfA&s',
                'category' => $stock->product->category->name,
            ];
        });

        return response()->json($formattedData);
    }



    public function indexForWarehouse()
    {
        $stocks = Stock::with(['product.category', 'unitprice'])->get();
        $formattedData = $stocks->map(function ($stock) {
                return [
                    'id' => $stock->id,
                    'name' => $stock->product->name,
                    'currentStock' => $stock->quantity,
                    'reorderLevel' => $stock->safety_stock,
                    'lastRestockDate' => $stock->updated_at,
                ];
            });
        return response()->json($formattedData);
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

    public function updateStock(Request $request)
    {
        $productId = $request->get('productId');
        $quantity = $request->get('quantity');
        $stock = Stock::findOrFail($productId);
        if (!$stock) {
            return response()->json(['message' => 'Stock not found'], 404);
        }
        $stock->quantity += $quantity;
        $stock->save();
        return response()->json(['message' => 'Stock updated successfully', 'stock' => $stock]);
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

    public function stockCountByCategory($categoryId)
    {
        try {
            $stocks = Stock::whereHas('product', function ($query) use ($categoryId) {
                $query->where('category_id', $categoryId);
            })->with('product')->get();

            $stockCount = $stocks->groupBy('product_id')->map(function ($stockGroup) {
                return $stockGroup->sum('quantity');
            });

            return response()->json($stockCount);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to calculate stock by category', 'message' => $e->getMessage()], 500);
        }
    }

    public function lowStock($top){
        try {
            $lowStockProducts = Stock::with('product')
            ->whereColumn('quantity', '<', 'safety_stock')
            ->orderBy('quantity', 'asc')
            ->take($top)
            ->get();

            return response()->json($lowStockProducts);
        }catch(Exception $e){
            return response()->json(['error'=>'Failed to get top '+$top+' low stock','message'=>$e->getMessage()]);
        }
    }



}
