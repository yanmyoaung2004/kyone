<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Customer;
use App\Models\Location;

class OrderSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get random customer and location IDs
        $customerIds = Customer::pluck('id')->toArray();
        $locationIds = Location::pluck('id')->toArray();

        // Define possible order statuses
        $statuses = ["pending", "processing", "completed", "cancelled"];

        foreach (range(1, 50) as $index) {
            DB::table('orders')->insert([
                'customer_id' => $faker->randomElement($customerIds),
                'status' => $faker->randomElement($statuses),
                'total_price' => $faker->randomFloat(2, 100, 1000),
                'location_id' => $faker->randomElement($locationIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
