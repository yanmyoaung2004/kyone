<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $warehouses = [
            ['name' => 'Warehouse 1', 'address' => 'Yangon', 'phone' => '0923455'],
            ['name' => 'Warehouse 2', 'address' => 'Mandalay', 'phone' => '0923456'],
            ['name' => 'Warehouse 3', 'address' => 'Naypyidaw', 'phone' => '0923457'],
            ['name' => 'Warehouse 4', 'address' => 'Bago', 'phone' => '0923458'],
            ['name' => 'Warehouse 5', 'address' => 'Pathein', 'phone' => '0923459'],
            ['name' => 'Warehouse 6', 'address' => 'Taunggyi', 'phone' => '0923460'],
            ['name' => 'Warehouse 7', 'address' => 'Mawlamyine', 'phone' => '0923461'],
            ['name' => 'Warehouse 8', 'address' => 'Pyay', 'phone' => '0923462'],
            ['name' => 'Warehouse 9', 'address' => 'Meiktila', 'phone' => '0923463'],
            ['name' => 'Warehouse 10', 'address' => 'Hpa-An', 'phone' => '0923464'],
        ];

        foreach ($warehouses as $warehouse) {
            \App\Models\Warehouse::create([
                'name' => $warehouse['name'],
                'address' => $warehouse['address'],
                'phone' => $warehouse['phone'],
            ]);
        }
    }
}