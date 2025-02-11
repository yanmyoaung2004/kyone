<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        $this->call([
            UserRoleSeeder::class,
            UserSeeder::class,
            DriverSeeder::class,
            CustomerSeeder::class,
            CategorySeeder::class,
            BrandSeeder::class,
            CitySeeder::class,
            LocationSeeder::class,
            ProductSeeder::class,
            UnitPriceSeeder::class,
            StockSeeder::class,
            OrderSeeder::class,
            OrderProductSeeder::class,
            InvoiceSeeder::class,
            TruckSeeder::class,
            OrderAssignTruckSeeder::class,
            ComplaintSeeder::class,
            OrderReturnSeeder::class


            
        ]);
    }
}
