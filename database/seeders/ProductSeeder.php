<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = Category::pluck('id')->toArray(); // Get all category IDs
        $brands = Brand::pluck('id')->toArray(); // Get all brand IDs

        $products = [
            'Laptop', 'Smartphone', 'Tablet', 'Smartwatch', 'Monitor', 'Keyboard', 
            'Mouse', 'Printer', 'Scanner', 'Router', 'External Hard Drive', 
            'USB Flash Drive', 'Graphics Card', 'Power Supply', 'Processor', 
            'Motherboard', 'RAM', 'Cooling Fan', 'Gaming Chair', 'Headphones', 
            'Speakers', 'Webcam', 'Microphone', 'Projector', 'VR Headset', 
            'Smart TV', 'Wireless Charger', 'Bluetooth Speaker', 'Smart Home Hub', 
            '3D Printer', 'Gaming Mouse', 'Mechanical Keyboard', 'Ethernet Switch', 
            'SSD Drive', 'HDD Drive', 'Laptop Cooling Pad', 'Smart Doorbell', 
            'Gaming Monitor', 'Smart Light Bulb', 'Wireless Earbuds', 'Security Camera',
            'Dash Camera', 'E-Reader', 'Smart Thermostat', 'Graphics Tablet', 
            'Soundbar', 'Home Theater System', 'VR Gloves', 'Portable Power Bank'
        ];

        foreach ($products as $product) {
            Product::create([
                'name' => $product,
                'description' => 'High-quality ' . Str::lower($product) . ' with advanced features.',
                'category_id' => $categories[array_rand($categories)], // Assign a random category
                'brand_id' => $brands[array_rand($brands)], // Assign a random brand
            ]);
        }
    }
}
