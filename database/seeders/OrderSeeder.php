<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\Customer;
use App\Models\Location;
use Illuminate\Support\Carbon;

class OrderSeeder extends Seeder
{
    // public function run()
    // {
    //     $faker = Faker::create();

    //     // Get random customer and location IDs
    //     $customerIds = Customer::pluck('id')->toArray();
    //     $locationIds = Location::pluck('id')->toArray();

    //     // Define possible order statuses
    //     $statuses = ["pending", "inprogress", "delayed", "delivered", "cancelled"];

    //     foreach (range(1, 50) as $index) {
    //         DB::table('orders')->insert([
    //             'customer_id' => $faker->randomElement($customerIds),
    //             'status' => $faker->randomElement($statuses),
    //             'total_price' => $faker->randomFloat(2, 100, 1000),
    //             'location_id' => $faker->randomElement($locationIds),
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);
    //     }
    // }

    public function run()
    {
        // Fetch customer IDs from the existing customers table
        $customerIds = DB::table('customers')->pluck('id')->toArray();
        
        // Fetch location IDs from the locations table
        $locationIds = DB::table('locations')->pluck('id')->toArray();

        $years = [2020, 2021, 2022, 2023, 2024, 2025];
        $months = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', 
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', 
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];
        
        // Loop over the years
        foreach ($years as $year) {
            foreach ($months as $monthNumber => $monthName) {
                $daysInMonth = Carbon::create($year, $monthNumber, 1)->daysInMonth;

                // Randomize the number of orders for each month
                $orderCount = rand(50, 200); // Randomize order count between 50 and 200

                // Insert orders for random days of the month
                for ($i = 0; $i < $orderCount; $i++) {
                    DB::table('orders')->insert([
                        'customer_id' => $customerIds[array_rand($customerIds)], // Random customer ID
                        'location_id' => $locationIds[array_rand($locationIds)], // Random location ID
                        'total_price' => rand(1000, 5000), // Random total price between 1000 and 5000
                        'status' => $this->getRandomOrderStatus(),
                        'created_at' => Carbon::create($year, $monthNumber, rand(1, $daysInMonth), rand(1, 23), rand(0, 59), rand(0, 59)),
                        'updated_at' => Carbon::create($year, $monthNumber, rand(1, $daysInMonth), rand(1, 23), rand(0, 59), rand(0, 59))
                    ]);
                }
            }
        }
    }

    // Helper function to get random order status
    private function getRandomOrderStatus()
    {
        $statuses = ["pending", "inprogress", "delayed", "delivered", "cancelled"];
        return $statuses[array_rand($statuses)];
    }
}
