<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            DB::table('locations')->insert([
                'address' => $faker->streetAddress,
                'state'   => $faker->state,
                'city'    => $faker->city,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
