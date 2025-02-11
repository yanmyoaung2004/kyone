<?php

namespace Database\Seeders;

use App\Constants\Role as ConstantsRole;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserRoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $roles = [
            ConstantsRole::CUSTOMER,
            ConstantsRole::SALE_MANAGER,
            ConstantsRole::DRIVER,
            ConstantsRole::WAREHOUSE_MANAGER,
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        $users = [
            ['name' => 'Customer User', 'email' => 'customer@example.com', 'password' => bcrypt('password')],
            ['name' => 'Sale Manager User', 'email' => 'sale@example.com', 'password' => bcrypt('password')],
            ['name' => 'Driver User', 'email' => 'driver@example.com', 'password' => bcrypt('password')],
            ['name' => 'Warehouse Manager User', 'email' => 'warehouse@example.com', 'password' => bcrypt('password')],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(['email' => $userData['email']], $userData);
            if ($user->email === 'customer@example.com') {
                $user->assignRole(ConstantsRole::CUSTOMER);
            } elseif ($user->email === 'sale@example.com') {
                $user->assignRole(ConstantsRole::SALE_MANAGER);
            } elseif ($user->email === 'driver@example.com') {
                $user->assignRole(ConstantsRole::DRIVER);
            } elseif ($user->email === 'warehouse@example.com') {
                $user->assignRole(ConstantsRole::WAREHOUSE_MANAGER);
            }
        }
    }
}
