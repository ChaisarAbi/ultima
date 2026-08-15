<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\PythonIntegrationService;
use App\Models\PredictionResult;

class ProcessLSTMPrediction implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $steps;
    protected $metric;

    public function __construct($steps = 7, $metric = 'total_services')
    {
        $this->steps = $steps;
        $this->metric = $metric;
    }

    public function handle(PythonIntegrationService $service)
    {
        $results = $service->forecast($this->metric, $this->steps);
        foreach ($results as $item) {
            PredictionResult::updateOrCreate(
                ['target_date' => $item['date'], 'metric' => $this->metric],
                ['predicted_value' => $item['value'], 'generated_at' => now()]
            );
        }
    }
}