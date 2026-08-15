<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class SparePartFactory extends Factory
{
    protected $model = SparePart::class;

    public function definition(): array
    {
        static $index = 0;
        $names = [
            'Oli Mesin 10W-40', 'Filter Oli', 'Busi Iridium', 'Kampas Rem Depan',
            'Kampas Rem Belakang', 'Aki GS 45Ah', 'Lampu LED', 'V-belt',
            'Filter Udara', 'Filter AC', 'Shockbreaker Kanan', 'Shockbreaker Kiri',
            'Bearing Roda', 'Seal Klep', 'Radiator Coolant', 'Fan Belt',
            'Alternator', 'Starter Motor', 'Tie Rod End', 'Ball Joint',
            'Bush Arm', 'Engine Mounting', 'Gearbox Oil', 'Power Steering Oil',
        ];

        return [
            'name' => $names[$index % count($names)],
            'part_number' => 'SP-' . str_pad(++$index, 4, '0', STR_PAD_LEFT),
            'stock' => $this->faker->numberBetween(0, 50),
            'minimum_stock' => $this->faker->numberBetween(2, 10),
            'price' => $this->faker->numberBetween(15000, 750000),
        ];
    }
}
