<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            ['name' => 'Laptops'],
            ['name' => 'Desktops'],
            ['name' => 'Monitors'],
            ['name' => 'Printers'],
            ['name' => 'Keyboards'],
            ['name' => 'Mice'],
            ['name' => 'Speakers'],
            ['name' => 'Headphones'],
            ['name' => 'Smartphones'],
            ['name' => 'Tablets'],
            ['name' => 'Smartwatches'],
            ['name' => 'Gaming Consoles'],
            ['name' => 'Graphic Cards'],
            ['name' => 'Processors'],
            ['name' => 'Motherboards'],
            ['name' => 'Storage Devices'],
            ['name' => 'RAM'],
            ['name' => 'Networking Equipment'],
            ['name' => 'Projectors'],
            ['name' => 'TVs'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
