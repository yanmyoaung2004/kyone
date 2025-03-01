<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
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

    public function warehouseOrderAvailability(Request $request)
    {
        $warehouseName = $request->get('warehouse');
        $warehouse = Warehouse::where('address', $warehouseName)->first();
        if (!$warehouse) {
            return response()->json(['status' => 'Warehouse not found'], 404);
        }
        $orders = $request->get('orders');
        $orderProducts = Order::with('products')->whereIn('id', $orders)->get()->pluck('products')->flatten();


        $productsGroupedById = $orderProducts->groupBy('id');

        $insufficientStock = [];

        foreach ($productsGroupedById as $productId => $groupedProducts) {
            $requiredQuantity = $groupedProducts->sum(function($product) {
                return $product->pivot->quantity;
            });
            $productData = Product::find($productId);

            $warehouseProduct = WarehouseProduct::where([
                'warehouse_id' => $warehouse->id,
                'product_id' => $productId,
            ])->first();


            if (!$warehouseProduct) {
                $insufficientStock[] = [
                    'status' => 'Product not found in warehouse',
                    'product_id' => $productId,
                    'product_name' => $productData->name,
                    'available_quantity' => 0,
                    'required_quantity' => $requiredQuantity
                ];
                continue;
            }

            if ($warehouseProduct->quantity < $requiredQuantity) {
                $insufficientStock[] = [
                    'status' => 'Product not enough in ' . $warehouse->name,
                    'product_id' => $productId,
                    'product_name' => $productData->name,
                    'available_quantity' => $warehouseProduct->quantity,
                    'required_quantity' => $requiredQuantity
                ];
               continue;
            }
        }
        if (count($insufficientStock) > 0) {
            return response()->json(['data' => $insufficientStock, 'success' => false]);
        }
        return response()->json(['status' => 'All orders can be fulfilled', 'success' => true, 'warehouseId' => $warehouse->id]);
    }

    public function warehousesThatCanFulfillOrders(Request $request)
    {
        $orders = $request->get('orders');

        if (!$orders || !is_array($orders)) {
            return response()->json(['status' => 'Invalid orders input', 'success' => false], 400);
        }
        $orderProducts = Order::with('products')
            ->whereIn('id', $orders)
            ->get()
            ->pluck('products')
            ->flatten();

        $productsGroupedById = $orderProducts->groupBy('id');

        $requiredQuantities = $productsGroupedById->mapWithKeys(function ($groupedProducts, $productId) {
            return [$productId => $groupedProducts->sum(fn($product) => $product->pivot->quantity)];
        });
        $warehouses = Warehouse::all();

        $fulfillingWarehouses = [];

        foreach ($warehouses as $warehouse) {
            $canFulfill = true;

            foreach ($requiredQuantities as $productId => $requiredQuantity) {
                $productData = Product::find($productId);
                $warehouseProduct = WarehouseProduct::where([
                    'warehouse_id' => $warehouse->id,
                    'product_id' => $productId,
                ])->first();

                if (!$warehouseProduct || $warehouseProduct->quantity < $requiredQuantity) {
                    $canFulfill = false;
                    break;
                }
            }

            if ($canFulfill) {
                $fulfillingWarehouses[] = [
                    'warehouse_id' => $warehouse->id,
                    'product_name' => $productData->name,
                    'warehouse_name' => $warehouse->name,
                    'warehouse_address' => $warehouse->address
                ];
            }
        }

        if (empty($fulfillingWarehouses)) {
            return response()->json(['status' => 'No warehouse can fulfill all orders', 'success' => false, 'data' => []], 200);
        }

        return response()->json(['data' => $fulfillingWarehouses, 'success' => true]);
    }





}
