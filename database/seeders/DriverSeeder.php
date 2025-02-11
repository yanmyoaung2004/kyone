<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Driver;
use App\Models\User; // Assuming User model is being used for user_id reference
use Faker\Factory as Faker;

class DriverSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Loop to create 20 drivers
        foreach (range(1, 20) as $index) {
            // Get a random user ID (user_id) from the User model
            $userId = User::inRandomOrder()->first()->id; // Assuming there is a user table

            // Create the driver
            Driver::create([
                'user_id' => $userId,
                'driver_license' => $faker->unique()->word,
                'nrc_number' => $faker->unique()->word,
                'phone' => $faker->phoneNumber,
                'status' => $faker->randomElement(['free', 'busy']),
            ]);
        }
    }
}
