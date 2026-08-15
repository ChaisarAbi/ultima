<?php

namespace Database\Factories;

use App\Models\OperationalLog;
use Illuminate\Database\Eloquent\Factories\Factory;

class OperationalLogFactory extends Factory
{
    protected $model = OperationalLog::class;

    public function definition(): array
    {
        $completed = $this->faker->numberBetween(2, 15);
        $total = $completed + $this->faker->numberBetween(1, 8);
        
        return [
            'total_services' => $total,
            'completed_services' => $completed,
            'avg_completion_hours' => $this->faker->randomFloat(1, 4, 48),
            'total_spare_used' => $this->faker->numberBetween(5, 40),
            'total_revenue' => $this->faker->numberBetween(1000000, 15000000),
        ];
    }
}