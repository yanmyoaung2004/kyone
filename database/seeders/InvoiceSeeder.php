<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Support\Str;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get all orders
        $orders = Order::all();

        // Loop through orders and create an invoice for each
        foreach ($orders as $order) {
            // Generate a random invoice number
            $invoiceNumber = 'INV-' . Str::upper(Str::random(10));

            // Create an invoice for the order
            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => $invoiceNumber,
                'total_amount' => rand(100, 1000), // Random total amount between 100 and 1000
            ]);
        }
    }
}
