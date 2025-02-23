<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\Truck;
use App\Models\Driver;
use Faker\Factory as Faker;

class OrderAssignTruckSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get random order, truck, and driver IDs
        $orderIds = Order::pluck('id')->toArray();
        $truckIds = Truck::pluck('id')->toArray();
        $driverIds = Driver::pluck('id')->toArray();

        foreach (range(1, 50) as $index) {
            DB::table('order_assign_trucks')->insert([
                'order_id' => $faker->randomElement($orderIds),
                'driver_id' => $faker->randomElement($driverIds),
                'truck_id' => $faker->randomElement($truckIds),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
