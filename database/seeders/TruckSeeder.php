<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Truck;
use Faker\Factory as Faker;

class TruckSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $index) {
            Truck::create([
                'license_plate' => strtoupper($faker->unique()->bothify('??-####')), // Example: AB-1234
                'status' => $this->getRandomStatus(),
            ]);
        }
    }

    private function getRandomStatus(): string
    {
        return ['free', 'busy'][array_rand(['free', 'busy'])];
    }
}
