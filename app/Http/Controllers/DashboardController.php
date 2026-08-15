<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\OperationalLog;
use App\Models\PredictionResult;
use App\Models\SparePart;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Role-based dashboard
        if ($user->hasRole('teknisi')) {
            return $this->teknisiDashboard($user);
        }

        // Statistik utama — safe defaults jika data kosong
        $totalServices = Service::count() ?: 0;
        $activeServices = Service::whereIn('status', ['pending', 'progress'])->count() ?: 0;
        $completedToday = Service::whereDate('completion_date', today())->count() ?: 0;
        $completedMonth = Service::whereMonth('completion_date', now()->month)
            ->whereYear('completion_date', now()->year)->count() ?: 0;
        $lowStockParts = SparePart::lowStock()->count() ?: 0;
        $totalRevenue = OperationalLog::inMonth(now()->month, now()->year)->sum('total_revenue') ?: 0;

        // Rata-rata waktu pengerjaan
        $avgCompletionHours = OperationalLog::inMonth(now()->month, now()->year)
            ->whereNotNull('avg_completion_hours')
            ->avg('avg_completion_hours') ?: 0;

        // Grafik 14 hari terakhir
        $recentLogs = collect();
        $logsQuery = OperationalLog::orderBy('log_date', 'desc')->limit(14)->get();
        if ($logsQuery->isNotEmpty()) {
            $recentLogs = $logsQuery->reverse()->values()
                ->map(function ($l) {
                    $l->log_date = date('Y-m-d', strtotime($l->log_date));
                    return $l;
                });
        }

        // Prediksi
        $predictions = collect();
        $predQuery = PredictionResult::where('metric', 'total_services')
            ->orderBy('target_date', 'desc')
            ->limit(14)
            ->get();
        if ($predQuery->isNotEmpty()) {
            $predictions = $predQuery->reverse()->values()
                ->map(function ($p) {
                    $p->target_date = date('Y-m-d', strtotime($p->target_date));
                    return $p;
                });
        }

        // Future predictions
        $futurePredictions = collect();
        $futureQuery = PredictionResult::where('metric', 'total_services')
            ->where('target_date', '>=', now()->format('Y-m-d'))
            ->orderBy('target_date')
            ->limit(7)
            ->get();
        if ($futureQuery->isNotEmpty()) {
            $futurePredictions = $futureQuery->values()
                ->map(function ($p) {
                    $p->target_date = date('Y-m-d', strtotime($p->target_date));
                    return $p;
                });
        }

        // Servis berdasarkan status
        $servicesByStatus = [
            'pending' => Service::where('status', 'pending')->count(),
            'in_progress' => Service::where('status', 'progress')->count(),
            'completed' => Service::where('status', 'done')->count(),
            'cancelled' => Service::where('status', 'cancelled')->count(),
        ];

        // Revenue 7 hari terakhir
        $weeklyRevenue = OperationalLog::lastDays(7)->sum('total_revenue') ?: 0;

        // Revenue bulan lalu
        $lastMonthRevenue = OperationalLog::inMonth(now()->subMonth()->month, now()->subMonth()->year)
            ->sum('total_revenue') ?: 0;

        // Pertumbuhan revenue
        $revenueGrowth = $lastMonthRevenue > 0
            ? round((($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Spare parts paling laris
        $topSpareParts = DB::table('service_spare_part')
            ->join('spare_parts', 'service_spare_part.spare_part_id', '=', 'spare_parts.id')
            ->select('spare_parts.name', DB::raw('SUM(service_spare_part.quantity) as total_used'))
            ->groupBy('spare_parts.id', 'spare_parts.name')
            ->orderByDesc('total_used')
            ->limit(5)
            ->get();

        // Recent activities
        $recentActivities = ActivityLog::with('user')->latest()->limit(10)->get();

        return view('dashboard', compact(
            'totalServices', 'activeServices', 'completedToday', 'completedMonth',
            'lowStockParts', 'totalRevenue', 'avgCompletionHours',
            'recentLogs', 'predictions', 'futurePredictions', 'servicesByStatus',
            'weeklyRevenue', 'revenueGrowth', 'topSpareParts', 'recentActivities'
        ));
    }

    protected function teknisiDashboard($user)
    {
        $assignedServices = Service::whereHas('technicians', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['vehicle.customer'])->latest()->limit(10)->get();

        $pendingServices = Service::whereHas('technicians', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->whereIn('status', ['pending', 'progress'])->count();

        $completedServices = Service::whereHas('technicians', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->where('status', 'done')->count();

        return view('dashboard-teknisi', compact(
            'assignedServices', 'pendingServices', 'completedServices'
        ));
    }
}