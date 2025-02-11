<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            ['name' => 'Yangon', 'eta' => '1 hour'],
            ['name' => 'Mandalay', 'eta' => '2 hours'],
            ['name' => 'Naypyidaw', 'eta' => '1.5 hours'],
            ['name' => 'Bago', 'eta' => '1.2 hours'],
            ['name' => 'Pathein', 'eta' => '3 hours'],
            ['name' => 'Taunggyi', 'eta' => '4 hours'],
            ['name' => 'Mawlamyine', 'eta' => '2.5 hours'],
            ['name' => 'Sittwe', 'eta' => '5 hours'],
            ['name' => 'Myitkyina', 'eta' => '6 hours'],
            ['name' => 'Hpa-An', 'eta' => '3.5 hours'],
            // Add more cities as needed
        ];

        foreach ($cities as $city) {
            \App\Models\City::create([
                'name' => $city['name'],
                'eta' => $city['eta'],
            ]);
        }
    }
}
