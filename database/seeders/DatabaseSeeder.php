<?php

namespace Database\Seeders;

use Carbon\Carbon;
use CategorySeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {



        DB::table('categories')->insert([
            [
                'id' => 1,
                'name' => 'Electronics',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Furniture',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // User::factory(10)->create();
        DB::table('products')->insert([
            [
                'id' => 1,
                'name' => 'Product A',
                'description' => 'Description for Product A',
                'category_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'name' => 'Product B',
                'description' => 'Description for Product B',
                'category_id' => 2,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        DB::table('stocks')->insert([
            [
                'id' => 1,
                'product_id' => 1,
                'quantity' => 10,
                'safety_stock' => 10,
                'unitprice_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => 2,
                'product_id' => 1,
                'quantity' => 10,
                'safety_stock' => 10,
                'unitprice_id' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],

        ]);



        //$this->call([CategorySeeder::class, ProductSeeder::class]);
    }
}
