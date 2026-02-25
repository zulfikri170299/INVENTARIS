<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Admin
        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin@inventaris.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
                'role' => 'Super Admin',
                'satker_id' => null,
            ]
        );

        // Super Admin 2
        \App\Models\User::updateOrCreate(
            ['email' => 'superadmin2@inventaris.com'],
            [
                'name' => 'Super Admin 2',
                'password' => bcrypt('password'),
                'role' => 'Super Admin 2',
                'satker_id' => null,
            ]
        );

        // Admin Satker (Satker ID 1 assumed to exist after SatkerSeeder runs)
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@satker.com'],
            [
                'name' => 'Admin Satker Jaksel',
                'password' => bcrypt('password'),
                'role' => 'Admin Satker',
                'satker_id' => 1,
            ]
        );

        // Pimpinan
        \App\Models\User::updateOrCreate(
            ['email' => 'pimpinan@inventaris.com'],
            [
                'name' => 'Bapak Pimpinan',
                'password' => bcrypt('password'),
                'role' => 'Pimpinan',
                'satker_id' => null,
            ]
        );
    }
}
