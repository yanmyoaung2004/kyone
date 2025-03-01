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
            ['name' => 'Yangon', 'eta' => '30 minutes', 'shippingCost'=> 0.5],
            ['name' => 'Mandalay', 'eta' => '6 hours', 'shippingCost' => 6],
            ['name' => 'Naypyidaw', 'eta' => '4 hours', 'shippingCost' => 4],
            ['name' => 'Bago', 'eta' => '1.5 hours', 'shippingCost' => 1.5],
            ['name' => 'Pyin Oo Lwin', 'eta' => '7 hours', 'shippingCost' => 7],
            ['name' => 'Pathein', 'eta' => '3.5 hours', 'shippingCost' => 3.5],
            ['name' => 'Bagan', 'eta' => '6.5 hours', 'shippingCost' => 6.5],
            ['name' => 'Mawlamyine', 'eta' => '5 hours', 'shippingCost' => 5],
            ['name' => 'Taunggyi', 'eta' => '7 hours', 'shippingCost' => 7],
            ['name' => 'Monywa', 'eta' => '10 hours', 'shippingCost' => 10],
            ['name' => 'Pyay', 'eta' => '5.5 hours', 'shippingCost' => 5.5],
            ['name' => 'Myitkyina', 'eta' => '14 hours', 'shippingCost' => 14],
            ['name' => 'Hpa-An', 'eta' => '4.5 hours', 'shippingCost' => 4.5],
            ['name' => 'Dawei', 'eta' => '13 hours', 'shippingCost' => 13],
            ['name' => 'Sittwe', 'eta' => '9 hours', 'shippingCost' => 9],
            ['name' => 'Lashio', 'eta' => '12 hours', 'shippingCost' => 12],
            ['name' => 'Meiktila', 'eta' => '5.5 hours', 'shippingCost' => 5.5],
            ['name' => 'Magway', 'eta' => '8 hours', 'shippingCost' => 8],
            ['name' => 'Shwebo', 'eta' => '11 hours', 'shippingCost' => 11],
            ['name' => 'Kalaw', 'eta' => '7 hours', 'shippingCost' => 7],
            ['name' => 'Kalay', 'eta' => '11 hours', 'shippingCost' => 11],
            ['name' => 'Yamethin', 'eta' => '6 hours', 'shippingCost' => 6],
            ['name' => 'Sagaing', 'eta' => '10 hours', 'shippingCost' => 10],
            ['name' => 'Loikaw', 'eta' => '11.5 hours', 'shippingCost' => 11.5],
            ['name' => 'Taungoo', 'eta' => '6 hours', 'shippingCost' => 6],
            ['name' => 'Thazi', 'eta' => '6 hours', 'shippingCost' => 6],
            ['name' => 'Pyawbwe', 'eta' => '6 hours', 'shippingCost' => 6],
            ['name' => 'Bhamo', 'eta' => '13 hours', 'shippingCost' => 13],
            ['name' => 'Hopin', 'eta' => '13.5 hours', 'shippingCost' => 13.5],
            ['name' => 'Hkamti', 'eta' => '14 hours', 'shippingCost' => 14],
            ['name' => 'Putao', 'eta' => '16 hours', 'shippingCost' => 16],
            ['name' => 'Mogaung', 'eta' => '14 hours', 'shippingCost' => 14],
            ['name' => 'Monghnyin', 'eta' => '14 hours', 'shippingCost' => 14],
        ];


        foreach ($cities as $city) {
            \App\Models\City::create([
                'name' => $city['name'],
                'eta' => $city['eta'],
                'shippingCost' => $city['shippingCost'],
            ]);
        }
    }
}