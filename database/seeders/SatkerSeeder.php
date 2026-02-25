<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SatkerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Satker::create([
            'nama_satker' => 'Polres Metro Jakarta Selatan',
        ]);

        \App\Models\Satker::create([
            'nama_satker' => 'Polres Metro Jakarta Barat',
        ]);
        
        \App\Models\Satker::create([
            'nama_satker' => 'Polda Metro Jaya',
        ]);
    }
}
