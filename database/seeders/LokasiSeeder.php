<?php

namespace Database\Seeders;

use App\Models\Lokasi;
use Illuminate\Database\Seeder;

class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            ['Jakarta', -6.2088, 106.8456],
            ['Bogor', -6.5950, 106.8166],
            ['Depok', -6.4025, 106.7942],
            ['Tangerang', -6.1783, 106.6319],
            ['Tangerang Selatan', -6.2886, 106.7179],
            ['Bekasi', -6.2383, 106.9756],
        ];

        foreach ($daftar as [$nama, $lat, $lon]) {
            Lokasi::create(['nama' => $nama, 'latitude' => $lat, 'longitude' => $lon]);
        }
    }
}