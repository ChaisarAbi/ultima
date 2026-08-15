<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\PythonIntegrationService;
use App\Models\PredictionResult;

class RunLSTMPrediction extends Command
{
    protected $signature = 'lstm:predict {--steps=7} {--metric=total_services}';
    protected $description = 'Run LSTM forecasting and save results';

    public function handle(PythonIntegrationService $service)
    {
        $steps = $this->option('steps');
        $metric = $this->option('metric');
        
        $this->info("Starting prediction for {$metric} with {$steps} steps...");
        
        try {
            $results = $service->forecast($metric, $steps);
            
            foreach ($results as $item) {
                PredictionResult::create([
                    'target_date' => $item['date'],
                    'metric' => $metric,
                    'predicted_value' => $item['value'],
                    'generated_at' => now(),
                ]);
            }
            
            $this->info("Successfully saved " . count($results) . " predictions.");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
        return 0;
    }
}