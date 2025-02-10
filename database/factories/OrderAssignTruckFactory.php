<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderAssignTruck>
 */
class OrderAssignTruckFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'truck_id' => fake()->randomNumber(5),
            'order_id' => Order::factory(),
            'driver_id' => fake()->randomNumber(5),
        ];
    }
}
