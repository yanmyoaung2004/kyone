<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\Order;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'invoice_number' => fake()->word(),
            'issue_date' => fake()->dateTime(),
            'due_date' => fake()->dateTime(),
            'total_amount' => fake()->word(),
            'status' => fake()->randomElement(["unpaid","paid","cancelled"]),
        ];
    }
}
