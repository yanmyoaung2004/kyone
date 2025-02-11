<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\User;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::pluck('id')->toArray(); // Get all user IDs

        $customers = [
            [
                'user_id' => $users[array_rand($users)], // Random user ID
                'phone' => '123-456-7890',
                'address' => '123 Main St, City, Country',
            ],
            [
                'user_id' => $users[array_rand($users)],
                'phone' => '987-654-3210',
                'address' => '456 Oak St, Town, Country',
            ],
            [
                'user_id' => $users[array_rand($users)],
                'phone' => '555-666-7777',
                'address' => '789 Pine St, Village, Country',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
