<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Customer;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Loop to create 50 orders
        foreach (range(1, 50) as $index) {
            // Get a random customer ID
            $customerId = Customer::inRandomOrder()->first()->id;

            // Generate a random status
            $status = $this->getRandomStatus();

            // Generate a random total price between 100 and 1000
            $totalPrice = $faker->randomFloat(2, 100, 1000);

            // Insert the order
            Order::create([
                'customer_id' => $customerId,
                'status' => $status,
                'total_price' => $totalPrice,
            ]);
        }
    }

    // Helper function to randomly choose a status
    private function getRandomStatus()
    {
        $statuses = ["pending", "inprogress", "delayed", "delivered", "cancelled"];
        return $statuses[array_rand($statuses)];
    }
}
