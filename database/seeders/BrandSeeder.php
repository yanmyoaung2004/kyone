<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            ['name' => 'Apple'],
            ['name' => 'Samsung'],
            ['name' => 'Sony'],
            ['name' => 'Microsoft'],
            ['name' => 'Intel'],
            ['name' => 'AMD'],
            ['name' => 'NVIDIA'],
            ['name' => 'ASUS'],
            ['name' => 'Acer'],
            ['name' => 'HP'],
            ['name' => 'Dell'],
            ['name' => 'Lenovo'],
            ['name' => 'LG'],
            ['name' => 'Razer'],
            ['name' => 'Corsair'],
            ['name' => 'MSI'],
            ['name' => 'Gigabyte'],
            ['name' => 'Huawei'],
            ['name' => 'Logitech'],
            ['name' => 'Western Digital'],
        ];

        foreach ($brands as $brand) {
            Brand::create($brand);
        }
    }
}
