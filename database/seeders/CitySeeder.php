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
            ['name' => 'Yangon', 'eta' => '0 hours'],
            ['name' => 'Mandalay', 'eta' => '6 hours'],
            ['name' => 'Naypyidaw', 'eta' => '4 hours'],
            ['name' => 'Bago', 'eta' => '1.5 hours'],
            ['name' => 'Pyin Oo Lwin', 'eta' => '7 hours'],
            ['name' => 'Pathein', 'eta' => '3.5 hours'],
            ['name' => 'Bagan', 'eta' => '6.5 hours'],
            ['name' => 'Mawlamyine', 'eta' => '5 hours'],
            ['name' => 'Taunggyi', 'eta' => '7 hours'],
            ['name' => 'Monywa', 'eta' => '10 hours'],
            ['name' => 'Pyay', 'eta' => '5.5 hours'],
            ['name' => 'Myitkyina', 'eta' => '14 hours'],
            ['name' => 'Hpa-An', 'eta' => '4.5 hours'],
            ['name' => 'Dawei', 'eta' => '13 hours'],
            ['name' => 'Sittwe', 'eta' => '9 hours'],
            ['name' => 'Lashio', 'eta' => '12 hours'],
            ['name' => 'Meiktila', 'eta' => '5.5 hours'],
            ['name' => 'Magway', 'eta' => '8 hours'],
            ['name' => 'Shwebo', 'eta' => '11 hours'],
            ['name' => 'Kalaw', 'eta' => '7 hours'],
            ['name' => 'Kalay', 'eta' => '11 hours'],
            ['name' => 'Yamethin', 'eta' => '6 hours'],
            ['name' => 'Sagaing', 'eta' => '10 hours'],
            ['name' => 'Loikaw', 'eta' => '11.5 hours'],
            ['name' => 'Taungoo', 'eta' => '6 hours'],
            ['name' => 'Thazi', 'eta' => '6 hours'],
            ['name' => 'Pyawbwe', 'eta' => '6 hours'],
            ['name' => 'Bhamo', 'eta' => '13 hours'],
            ['name' => 'Hopin', 'eta' => '13.5 hours'],
            ['name' => 'Hkamti', 'eta' => '14 hours'],
            ['name' => 'Putao', 'eta' => '16 hours'],
            ['name' => 'Mogaung', 'eta' => '14 hours'],
            ['name' => 'Monghnyin', 'eta' => '14 hours'],
        ];

        foreach ($cities as $city) {
            \App\Models\City::create([
                'name' => $city['name'],
                'eta' => $city['eta'],
            ]);
        }
    }
}