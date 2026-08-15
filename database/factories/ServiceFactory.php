<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    protected $model = Service::class;

    public function definition(): array
    {
        $entry = $this->faker->dateTimeBetween('-60 days', 'now');
        $status = $this->faker->randomElement(['pending', 'progress', 'done', 'cancelled']);
        
        $completion = null;
        if ($status === 'done') {
            $completion = (clone $entry)->modify('+' . rand(1, 72) . ' hours');
        } elseif ($status === 'cancelled') {
            $completion = (clone $entry)->modify('+' . rand(1, 24) . ' hours');
        }

        return [
            'customer_name' => $this->faker->name(),
            'vehicle_plate' => strtoupper($this->faker->randomLetter() . $this->faker->randomLetter() . ' ' . $this->faker->numberBetween(1000, 9999) . ' ' . $this->faker->randomLetter() . $this->faker->randomLetter()),
            'type' => $this->faker->randomElement(['body_repair', 'engine', 'electrical']),
            'entry_date' => $entry,
            'completion_date' => $completion,
            'status' => $status,
            'total_cost' => $this->faker->numberBetween(200000, 5000000),
            'created_at' => $entry,
            'updated_at' => $entry,
        ];
    }
}