<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\UnitPrice;
use App\Models\OrderProduct;
use Illuminate\Support\Facades\DB;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all products and orders
        $orders = Order::all();
        $products = Product::all();

        // Loop through orders and associate them with products
        foreach ($orders as $order) {
            // Randomly pick products and their prices
            $product = $products->random(); // Get a random product
            $unitPrice = UnitPrice::where('product_id', $product->id)->first(); // Get the unit price for the product

            // Attach the product to the order in the order_product pivot table
            DB::table('order_product')->insert([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'unitprice_id' => $unitPrice->id,
                'quantity' => rand(1, 10), // Random quantity between 1 and 10
            ]);
        }
    }
}
