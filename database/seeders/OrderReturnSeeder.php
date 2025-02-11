<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OrderReturn;
use App\Models\Order;
use App\Models\Product;
use Faker\Factory as Faker;

class OrderReturnSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Loop to create 50 order returns
        foreach (range(1, 50) as $index) {
            // Get a random order ID
            $order = Order::inRandomOrder()->first();
            $orderId = $order ? $order->id : null;

            // Get a random product ID
            $product = Product::inRandomOrder()->first();
            $productId = $product ? $product->id : null;

            // Generate a random status
            $status = $this->getRandomStatus();

            // Insert the order return
            OrderReturn::create([
                'order_id' => $orderId,
                'product_id' => $productId,
                'quantity' => $faker->numberBetween(1, 5), // Random quantity between 1-5
                'reason' => ucfirst($faker->sentence(6, true)), // Random reason
                'status' => $status,
            ]);
        }
    }

    // Helper function to randomly choose a status
    private function getRandomStatus(): string
    {
        $statuses = ["pending", "inprogress", "delivered", "cancelled", "delayed"];
        return $statuses[array_rand($statuses)];
    }
}
