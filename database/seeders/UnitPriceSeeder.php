<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Unitprice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    $products = Product::all(); // Fetch all products

    foreach ($products as $product) {
        Unitprice::create([
            'product_id' => $product->id,
            'price' => rand(50, 5000), // Random price between 50 and 5000
        ]);
    }
}
}
