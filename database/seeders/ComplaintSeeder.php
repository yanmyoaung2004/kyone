<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;
use Faker\Factory as Faker;

class ComplaintSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Loop to create 50 complaints
        foreach (range(1, 50) as $index) {
            // Get a random customer ID
            $customerId = Customer::inRandomOrder()->first()->id;

            // Get a random order ID (nullable)
            $order = Order::inRandomOrder()->first();
            $orderId = $order ? $order->id : null;

            // Generate a random status and type
            $status = $this->getRandomStatus();
            $type = $this->getRandomType();

            // Insert the complaint
            Complaint::create([
                'customer_id' => $customerId,
                'order_id' => $orderId,
                'subject' => ucfirst($faker->words(3, true)), // Random subject
                'description' => $faker->sentence(10), // Random description
                'status' => $status,
                'type' => $type,
            ]);
        }
    }

    // Helper function to randomly choose a status
    private function getRandomStatus(): string
    {
        $statuses = ["open", "in_progress", "resolved", "closed"];
        return $statuses[array_rand($statuses)];
    }

    // Helper function to randomly choose a type
    private function getRandomType(): string
    {
        $types = ["delayed", "faulty", "wrong", "missing"];
        return $types[array_rand($types)];
    }
}
