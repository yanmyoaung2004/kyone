<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Complaint;
use App\Models\Customer;
use App\Models\Order;

class ComplaintFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Complaint::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'order_id' => Order::factory(),
            'subject' => fake()->word(),
            'description' => fake()->text(),
            'status' => fake()->randomElement(["open","in_progress","resolved","closed"]),
            'type' => fake()->randomElement(["delayed","faulty","wrong","missing"]),
        ];
    }
}
