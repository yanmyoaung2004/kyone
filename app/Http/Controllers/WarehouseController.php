<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\WarehouseImportProduct;
use App\Models\WarehouseProduct;
use App\Models\WarehouseTransfer;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class WarehouseController extends Controller
{
    /**
     * Display a list of all warehouses with optional products.
     */
    public function index(Request $request): JsonResponse
    {
        $withProducts = $request->boolean('with_products', false);

        $warehouses = Warehouse::select('id', 'name', 'address', 'phone', 'created_at')
            ->when($withProducts, fn($query) => $query->with(['products:id,warehouse_id,product_name,qty']))
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $warehouses,
        ]);
    }

    /**
     * Store a newly created warehouse.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:warehouses,name',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $warehouse = Warehouse::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Warehouse created successfully.',
                'warehouse' => $warehouse,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified warehouse.
     */
    public function show($id): JsonResponse
    {
        try {
            $warehouse = Warehouse::with(['products:id,warehouse_id,product_name,qty'])
                ->select('id', 'name', 'address', 'phone', 'created_at')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $warehouse,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.',
            ], 404);
        }
    }

    /**
     * Update the specified warehouse.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255|unique:warehouses,name,' . $id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Warehouse updated successfully.',
                'warehouse' => $warehouse,
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified warehouse.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $warehouse = Warehouse::findOrFail($id);
            $warehouse->delete();

            return response()->json([
                'success' => true,
                'message' => 'Warehouse deleted successfully.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found.',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getWarehouseProduct($id){
        $products = WarehouseProduct::with(['warehouse', 'product'])->where('warehouse_id', $id)->get();
        return response()->json($products);
    }

    public function warehouseTransfer(Request $request)
    {
        $validated = $request->validate([
            'fromWarehouse' => 'required|exists:warehouses,id',
            'toWarehouse' => 'required|exists:warehouses,id|different:fromWarehouse',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $warehouseFrom = $validated['fromWarehouse'];
            $warehouseTo = $validated['toWarehouse'];
            $products = $validated['products'];

            foreach ($products as $product) {
                $productId = $product['id'];
                $quantity = $product['quantity'];


                WarehouseTransfer::create([
                    'from_warehouse_id' => $warehouseFrom,
                    'to_warehouse_id' => $warehouseTo,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ]);

                $productTo = WarehouseProduct::firstOrCreate(
                    ['product_id' => $productId, 'warehouse_id' => $warehouseTo],
                    ['quantity' => 0]
                );
                $productTo->increment('quantity', $quantity);

                $productFrom = WarehouseProduct::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseFrom)
                    ->first();

                if (!$productFrom || $productFrom->quantity < $quantity) {
                    throw new \Exception("Insufficient stock for product ID {$productId} in the source warehouse.");
                }

                $productFrom->decrement('quantity', $quantity);
            }

            DB::commit();
            return response()->json(['message' => 'Warehouse transfer successful.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Warehouse transfer failed.', 'error' => $e->getMessage()], 500);
        }
    }


    public function warehouseProductAssign(Request $request)
    {
        $validated = $request->validate([
            'warehouseId' => 'required|exists:warehouses,id',
            'productId' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'purchase_product_id' => 'required'
        ]);

        DB::beginTransaction();

        try {
            WarehouseImportProduct::create([
                    'purchase_product_id' => $validated['purchase_product_id'],
                    'warehouse_id' => $validated['warehouseId'],
                    'quantity' => $validated['quantity'],
                ]);

            $warehouseProduct = WarehouseProduct::firstOrNew([
                'warehouse_id' => $validated['warehouseId'],
                'product_id' => $validated['productId'],
            ]);

            $warehouseProduct->quantity = $warehouseProduct->exists
                ? $warehouseProduct->quantity + $validated['quantity']
                : $validated['quantity'];

            $warehouseProduct->save();

            DB::commit();

            return response()->json([
                'message' => 'Warehouse product assigned successfully.',
                'warehouse_product' => $warehouseProduct,
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to assign product to warehouse.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}