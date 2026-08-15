<?php

namespace App\Http\Controllers;

use App\Models\PredictionResult;
use App\Models\OperationalLog;
use App\Jobs\ProcessLSTMPrediction;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PredictionController extends Controller
{
    public function index()
    {
        // Past predictions (last 30 days)
        $pastPredictions = PredictionResult::where('metric', 'total_services')
                        ->where('target_date', '<', now()->format('Y-m-d'))
                        ->orderBy('target_date', 'desc')
                        ->limit(30)
                        ->get()
                        ->reverse();

        // Future predictions (next 7 days)
        $futurePredictions = PredictionResult::where('metric', 'total_services')
                        ->where('target_date', '>=', now()->format('Y-m-d'))
                        ->orderBy('target_date')
                        ->limit(7)
                        ->get();

        // Combine for chart
        $allPredictions = collect($pastPredictions)->merge($futurePredictions)
            ->map(function ($p) { $p->target_date = date('Y-m-d', strtotime($p->target_date)); return $p; });

        // Metrics
        $accuracy = 0;
        $count = 0;
        foreach ($pastPredictions as $p) {
            if ($p->actual_value !== null) {
                $diff = abs($p->predicted_value - $p->actual_value);
                $max = max($p->predicted_value, $p->actual_value);
                $accuracy += $max > 0 ? (1 - $diff / $max) * 100 : 0;
                $count++;
            }
        }
        $avgAccuracy = $count > 0 ? round($accuracy / $count, 1) : 0;

        // Status prediksi
        $totalPredictions = PredictionResult::count();
        $withActual = PredictionResult::whereNotNull('actual_value')->count();
        $futureCount = PredictionResult::whereNull('actual_value')->count();

        // Logs for chart + table history
        $recentLogs = OperationalLog::orderBy('log_date', 'desc')->limit(30)->get()->reverse()
            ->map(function ($l) { $l->log_date = date('Y-m-d', strtotime($l->log_date)); return $l; })
            ->values();

        // ====== EXTRA DATA FOR NEW CHARTS ======

        // 1. Trend chart data (last 30 days for line chart, lighter)
        $allLogs = OperationalLog::orderBy('log_date', 'desc')->limit(30)->get()->reverse()
            ->map(function ($l) { $l->log_date = date('Y-m-d', strtotime($l->log_date)); return $l; })
            ->values();

        // 2. Revenue predictions — ambil dari total_services terus kalikan dengan rata-rata revenue
        $revenuePredictions = collect();
        $futureServicePreds = PredictionResult::where('metric', 'total_services')
            ->where('target_date', '>=', now()->format('Y-m-d'))
            ->orderBy('target_date')
            ->limit(14)
            ->get();
        // Hitung rasio revenue/servis dari data aktual
        $avgRevenuePerService = OperationalLog::where('total_services', '>', 0)->avg(
            DB::raw('total_revenue / total_services')
        ) ?: 200000;
        foreach ($futureServicePreds as $pred) {
            $clone = clone $pred;
            $clone->metric = 'total_revenue';
            $clone->predicted_value = round($pred->predicted_value * $avgRevenuePerService);
            $revenuePredictions->push($clone);
        }

        // 3. Revenue historical (30 hari terakhir)
        $revenueHistory = OperationalLog::orderBy('log_date', 'desc')->limit(30)->get()->reverse()
            ->map(function ($l) { 
                $l->log_date = date('Y-m-d', strtotime($l->log_date)); 
                $l->predicted_value = floatval($l->total_revenue);
                return $l; 
            })
            ->values();

        // 4. Prediction accuracy distribution
        $accuracyDist = [
            'sangat_akurat' => 0, // >90%
            'akurat' => 0,        // 70-90%
            'cukup' => 0,         // 50-70%
            'kurang' => 0,        // <50%
        ];
        foreach ($pastPredictions as $p) {
            if ($p->actual_value !== null && $p->actual_value > 0) {
                $acc = max(0, min(100, (1 - abs($p->predicted_value - $p->actual_value) / max($p->predicted_value, $p->actual_value)) * 100));
                if ($acc >= 90) $accuracyDist['sangat_akurat']++;
                elseif ($acc >= 70) $accuracyDist['akurat']++;
                elseif ($acc >= 50) $accuracyDist['cukup']++;
                else $accuracyDist['kurang']++;
            }
        }

        // 5. Weekly pattern (avg per day of week)
        $weeklyPattern = [];
        for ($d = 1; $d <= 7; $d++) {
            $dayLogs = OperationalLog::whereRaw('DAYOFWEEK(log_date) = ?', [$d + 1])->get();
            $weeklyPattern[$d] = $dayLogs->count() > 0 ? round($dayLogs->avg('total_services'), 1) : 0;
        }
        $dayNames = ['', 'Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'];

        // 6. Completed vs cancelled ratio
        $completedCount = OperationalLog::sum('completed_services');
        $totalServicesCount = OperationalLog::sum('total_services');
        $cancelledCount = max(0, $totalServicesCount - $completedCount);

        return view('prediction', compact(
            'pastPredictions', 'futurePredictions', 'allPredictions',
            'avgAccuracy', 'totalPredictions', 'withActual', 'futureCount',
            'recentLogs', 'allLogs', 'revenuePredictions', 'revenueHistory',
            'accuracyDist', 'weeklyPattern', 'dayNames',
            'completedCount', 'totalServicesCount', 'cancelledCount'
        ));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'steps' => 'integer|min:1|max:365',
            'metric' => 'in:total_services,completed_services,total_revenue,total_spare_used',
        ]);

        $steps = min((int) $request->input('steps', 7), 365);
        $metric = $request->input('metric', 'total_services');

        // Check data availability before dispatching
        $dataCount = OperationalLog::count();
        if ($dataCount < 10) {
            $errorMsg = "Data historis tidak mencukupi. Saat ini hanya {$dataCount} hari data. Minimal 10 hari.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $errorMsg], 422);
            }
            return redirect()->back()->with('error', $errorMsg);
        }

        // Dispatch job agar tidak timeout
        ProcessLSTMPrediction::dispatch($steps, $metric);

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "Prediksi {$steps} hari ke depan sedang diproses di background."]);
        }

        return redirect()->back()->with('success', "Prediksi {$steps} hari ke depan sedang diproses di background. Refresh halaman untuk melihat hasil.");
    }

    public function getLatest()
    {
        $predictions = PredictionResult::where('metric', 'total_services')
                ->orderBy('target_date', 'desc')
                ->limit(14)
                ->get()
                ->reverse()
                ->values();

        $history = OperationalLog::orderBy('log_date', 'desc')
                ->limit(14)
                ->get()
                ->reverse()
                ->values();

        return response()->json([
            'predictions' => $predictions,
            'history' => $history,
        ]);
    }

    public function getChartData()
    {
        $logs = OperationalLog::orderBy('log_date', 'desc')
            ->limit(30)
            ->get()
            ->reverse()
            ->values()
            ->map(function ($l) { 
                $l->log_date = date('Y-m-d', strtotime($l->log_date)); 
                return $l; 
            });

        $future = PredictionResult::where('metric', 'total_services')
                ->where('target_date', '>=', now()->format('Y-m-d'))
                ->orderBy('target_date')
                ->limit(7)
                ->get()
                ->map(function ($p) { 
                    $p->target_date = date('Y-m-d', strtotime($p->target_date)); 
                    return $p; 
                });

        return response()->json([
            'historical' => $logs,
            'predictions' => $future,
        ]);
    }

}
