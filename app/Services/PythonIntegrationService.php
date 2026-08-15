<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PythonIntegrationService
{
    /**
     * Run ARIMA forecasting via Python script.
     *
     * @param string $metric Column name in operational_logs (e.g. total_services, total_revenue)
     * @param int $steps Number of days to forecast
     * @return array Array of ['date' => 'Y-m-d', 'value' => float]
     * @throws \Exception
     */
    public function forecast(string $metric = 'total_services', int $steps = 7): array
    {
        // 1. Validate field exists by checking raw column
        $allowedMetrics = ['total_services', 'completed_services', 'total_spare_used', 'total_revenue'];
        if (!in_array($metric, $allowedMetrics)) {
            throw new \Exception("Metric '{$metric}' tidak valid. Gunakan: " . implode(', ', $allowedMetrics));
        }

        // 2. Fetch historical data
        $logs = \App\Models\OperationalLog::orderBy('log_date')
            ->limit(730)
            ->get(['log_date', $metric]);

        if ($logs->count() < 10) {
            throw new \Exception("Data historis kurang dari 10 hari. Saat ini hanya {$logs->count()} hari.");
        }

        // 3. Write CSV to temp
        $csvPath = storage_path('app/temp/input_' . time() . '_' . uniqid() . '.csv');
        $handle = fopen($csvPath, 'w');
        fputcsv($handle, ['log_date', 'value']);
        foreach ($logs as $row) {
            fputcsv($handle, [$row->log_date, $row->$metric]);
        }
        fclose($handle);

        // 4. Prepare command with escaped arguments
        $scriptPath = base_path('scripts/lstm_forecast.py');
        $outputPath = storage_path('app/temp/output_' . time() . '_' . uniqid() . '.json');
        $pythonPath = escapeshellcmd(env('PYTHON_PATH', 'python'));

        $escapedScript = escapeshellarg($scriptPath);
        $escapedInput = escapeshellarg($csvPath);
        $escapedOutput = escapeshellarg($outputPath);
        $escapedSteps = escapeshellarg((string) $steps);

        $command = "{$pythonPath} {$escapedScript} --input {$escapedInput} --output {$escapedOutput} --steps {$escapedSteps} 2>&1";

        // 5. Execute Python
        exec($command, $output, $returnCode);

        // 6. Parse and validate result
        $result = [];
        if ($returnCode === 0 && file_exists($outputPath)) {
            $raw = file_get_contents($outputPath);
            if ($raw === false || empty(trim($raw))) {
                throw new \Exception('Python script menghasilkan file output kosong.');
            }
            $decoded = json_decode($raw, true);
            if (!is_array($decoded)) {
                throw new \Exception('Python script menghasilkan JSON tidak valid: ' . substr($raw, 0, 200));
            }
            $result = $decoded;
        } else {
            $errorMsg = implode("\n", $output);
            Log::error("Python ARIMA Error: " . $errorMsg);
            throw new \Exception("Gagal menjalankan prediksi: " . ($errorMsg ?: 'Unknown error'));
        }

        // 7. Cleanup temp files
        @unlink($csvPath);
        @unlink($outputPath);

        return $result;
    }
}
