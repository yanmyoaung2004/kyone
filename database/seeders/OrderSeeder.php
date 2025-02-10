<?
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Product;
use App\Models\UnitPrice;
use App\Models\OrderProduct;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 random orders
        \App\Models\Order::factory(10)->create()->each(function ($order) {
            // Get a random product and its unit price
            $products = Product::all();
            $product = $products->random(); // Pick a random product

            $unitPrice = UnitPrice::where('product_id', $product->id)->first();

            // Attach products to orders with random quantities
            OrderProduct::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'unitprice_id' => $unitPrice->id,
                'quantity' => rand(1, 10), // Random quantity between 1 and 10
            ]);
        });
    }
}
