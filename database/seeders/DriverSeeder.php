<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Faker\Factory as Faker;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get random user IDs (assuming they exist)
        $userIds = User::pluck('id')->toArray();

        foreach (range(1, 20) as $index) {
            DB::table('drivers')->insert([
                'user_id' => $faker->randomElement($userIds),
                'driver_license' => $faker->unique()->bothify('DL-######'),
                'nrc_number' => $faker->unique()->bothify('NR-######'),
                'phone' => $faker->phoneNumber(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
