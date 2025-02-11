<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Stock;
use App\Models\Product;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all(); // Fetch all products

        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'quantity' => rand(10, 100), // Random quantity between 10 and 100
                'safety_stock' => rand(5, 20), // Random safety stock between 5 and 20
            ]);
        }
    }
}
