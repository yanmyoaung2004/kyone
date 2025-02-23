<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchasePrice;
use App\Models\PurchaseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseController extends Controller
{
    //

    public function createPurchase(Request $request)
    {
        $validated = $request->validate([
            'serviceCenter' => 'required|exists:service_centers,id',
            'products' => 'required|array|min:1',
          /*  'products.*.product_id' => 'required|exists:products,id',
            'products.*.purchase_price_id' => 'required|exists:purchase_prices,id',
            'products.*.quantity' => 'required|integer|min:1',*/
        ]);
        DB::beginTransaction();

        try {
            $invoiceNumber = 'INV' . strtoupper(uniqid());
            $purchase = Purchase::create([
                'service_center_id' => $validated['serviceCenter'],
                'invoice_number' => $invoiceNumber
            ]);

            foreach ($validated['products'] as $product) {
                $purchasePrice = PurchasePrice::where(
                    [
                        'price' => $product['price'],
                        'product_id' => $product['id'],
                    ])->first();
                if(!$purchasePrice){
                    $purchasePrice = PurchasePrice::create(
                    [
                        'price' => $product['price'],
                        'product_id' => $product['id'],
                    ]);
                }
                PurchaseProduct::create([
                    'product_id' => $product['id'],
                    'purchase_price_id' => $purchasePrice->id,
                    'quantity' => $product['quantity'],
                    'purchase_id' => $purchase->id,
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Purchase created successfully.',
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Purchase creation failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to create purchase.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPurchaseData(){
        $purchase = Purchase::with('serviceCenter')->get();
        return response()->json($purchase);
    }

    public function getPurchaseProductData($invoice_number){
        $purchase = Purchase::where('invoice_number', $invoice_number)->first();
        $products = PurchaseProduct::with('product', 'purchasePrice')->where('purchase_id', $purchase->id)->get();
        return response()->json($products);
    }
}
