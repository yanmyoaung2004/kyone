<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Assuming that the city table is already populated
        $cities = \App\Models\City::all(); // Fetch all cities

        // Loop to create 50 locations
        foreach (range(1, 50) as $index) {
            DB::table('locations')->insert([
                'address' => $faker->address,
                'state' => $faker->state,
                'city_id' => $cities->random()->id,  // Assigning a random city ID
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
