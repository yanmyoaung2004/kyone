<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ServiceCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceCenters = [
            [
                'name' => 'Yangon Service Center',
                'phone' => '09-123456789',
                'email' => 'yangon@example.com',
                'address' => 'No. 123, Yangon Street, Yangon, Myanmar',
                'credit_limit' => 500000,
            ],
            [
                'name' => 'Mandalay Service Center',
                'phone' => '09-987654321',
                'email' => 'mandalay@example.com',
                'address' => 'No. 456, Mandalay Road, Mandalay, Myanmar',
                'credit_limit' => 400000,
            ],
            [
                'name' => 'Naypyidaw Service Center',
                'phone' => '09-112233445',
                'email' => 'naypyidaw@example.com',
                'address' => 'No. 789, Capital Avenue, Naypyidaw, Myanmar',
                'credit_limit' => 600000,
            ],
            [
                'name' => 'Taunggyi Service Center',
                'phone' => '09-556677889',
                'email' => 'taunggyi@example.com',
                'address' => 'No. 321, Taunggyi Lane, Taunggyi, Myanmar',
                'credit_limit' => 350000,
            ],
            [
                'name' => 'Pathein Service Center',
                'phone' => '09-667788990',
                'email' => 'pathein@example.com',
                'address' => 'No. 654, Pathein Road, Pathein, Myanmar',
                'credit_limit' => 300000,
            ],
            [
                'name' => 'Mawlamyine Service Center',
                'phone' => '09-778899001',
                'email' => 'mawlamyine@example.com',
                'address' => 'No. 987, Mawlamyine Street, Mawlamyine, Myanmar',
                'credit_limit' => 450000,
            ],
            [
                'name' => 'Pyay Service Center',
                'phone' => '09-889900112',
                'email' => 'pyay@example.com',
                'address' => 'No. 210, Pyay Road, Pyay, Myanmar',
                'credit_limit' => 320000,
            ],
            [
                'name' => 'Hpa-An Service Center',
                'phone' => '09-990011223',
                'email' => 'hpa-an@example.com',
                'address' => 'No. 543, Hpa-An Avenue, Hpa-An, Myanmar',
                'credit_limit' => 310000,
            ],
            [
                'name' => 'Magway Service Center',
                'phone' => '09-101112131',
                'email' => 'magway@example.com',
                'address' => 'No. 876, Magway Street, Magway, Myanmar',
                'credit_limit' => 280000,
            ],
            [
                'name' => 'Meiktila Service Center',
                'phone' => '09-121314151',
                'email' => 'meiktila@example.com',
                'address' => 'No. 369, Meiktila Road, Meiktila, Myanmar',
                'credit_limit' => 340000,
            ],
        ];


        foreach ($serviceCenters as $center) {
            \App\Models\ServiceCenter::create([
                'name' => $center['name'],
                'phone' => $center['phone'],
                'email' => $center['email'],
                'address' => $center['address'],
                'credit_limit' => $center['credit_limit'],
            ]);
        }
    }
}