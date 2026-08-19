<?php

namespace Database\Seeders;

use App\Models\ServiceType;
use Illuminate\Database\Seeder;

class ServiceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $serviceTypes = [
            [
                'name' => 'Body Repair',
                'slug' => 'body_repair',
                'description' => 'Perbaikan atau pengecatan bodi kendaraan termasuk penggantian panel bodi.',
                'base_price' => 500000,
                'status' => 'active',
                'sort_order' => 1,
            ],
            [
                'name' => 'Engine Overhaul',
                'slug' => 'engine_overhaul',
                'description' => 'Perbaikan besar mesin meliputi pembongkaran, inspeksi, dan penggantian komponen.',
                'base_price' => 2000000,
                'status' => 'active',
                'sort_order' => 2,
            ],
            [
                'name' => 'Electrical System',
                'slug' => 'electrical_system',
                'description' => 'Perbaikan dan pemeliharaan sistem kelistrikan kendaraan.',
                'base_price' => 300000,
                'status' => 'active',
                'sort_order' => 3,
            ],
            [
                'name' => 'Transmission Service',
                'slug' => 'transmission_service',
                'description' => 'Perawatan atau perbaikan sistem transmisi dan kopling.',
                'base_price' => 1500000,
                'status' => 'active',
                'sort_order' => 4,
            ],
            [
                'name' => 'Tune Up',
                'slug' => 'tune_up',
                'description' => 'Perawatan berkala meliputi penggantian oli, filter, dan penyetelan mesin.',
                'base_price' => 750000,
                'status' => 'active',
                'sort_order' => 5,
            ],
            [
                'name' => 'Ban & Roda',
                'slug' => 'ban_dan_roda',
                'description' => 'Layanan terkait ban, roda, ALIGNMENT, BALAGING, dan penggantian ban.',
                'base_price' => 200000,
                'status' => 'active',
                'sort_order' => 6,
            ],
            [
                'name' => 'Rem & Suspensi',
                'slug' => 'rem_dan_suspensi',
                'description' => 'Perbaikan sistem rem (disc/drum brake) dan suspensi (shockbreaker, per daun).',
                'base_price' => 600000,
                'status' => 'active',
                'sort_order' => 7,
            ],
            [
                'name' => 'AC Service',
                'slug' => 'ac_service',
                'description' => 'Perbaikan dan perawatan AC kendaraan (refrigerant, compressor, condenser).',
                'base_price' => 400000,
                'status' => 'active',
                'sort_order' => 8,
            ],
        ];

        foreach ($serviceTypes as $type) {
            ServiceType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}