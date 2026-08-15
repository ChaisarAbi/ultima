<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\SparePart;
use App\Models\OperationalLog;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $search = $request->input('search', '');

        // Services with filtering — handle empty search gracefully
        $servicesQuery = Service::orderBy('created_at', 'desc');
        
        if ($filter !== 'all') {
            $servicesQuery->where('status', $filter);
        }
        
        if (!empty($search) && strlen(trim($search)) > 0) {
            $servicesQuery->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }
        
        $services = $servicesQuery->paginate(10);

        // Spare Parts — safe defaults
        $spareParts = SparePart::orderBy('name')->get();

        // Stats for monitoring — safe jika data kosong
        $todayServices = Service::whereDate('entry_date', today())->count() ?: 0;
        $todayCompleted = Service::whereDate('completion_date', today())->count() ?: 0;
        $todayRevenue = OperationalLog::where('log_date', today())->sum('total_revenue') ?: 0;
        $lowStockCount = SparePart::lowStock()->count() ?: 0;

        return view('monitoring', compact(
            'services', 'spareParts', 'filter', 'search',
            'todayServices', 'todayCompleted', 'todayRevenue', 'lowStockCount'
        ));
    }

    // API endpoint untuk spare parts
    public function spareParts()
    {
        $parts = SparePart::orderBy('name')->get();
        return response()->json($parts);
    }

    // Update stock spare part
    public function updateStock(Request $request, SparePart $sparePart)
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
        ]);

        $updateData = ['stock' => $request->stock];
        if ($request->has('minimum_stock')) {
            $updateData['minimum_stock'] = $request->minimum_stock;
        }

        $sparePart->update($updateData);

        return redirect()->back()->with('success', "Stock {$sparePart->name} berhasil diupdate");
    }

    // Update status service
    public function updateServiceStatus(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,done,cancelled',
        ]);

        $updateData = ['status' => $request->status];
        
        if ($request->status === 'done' && !$service->completion_date) {
            $updateData['completion_date'] = now();
        } elseif ($request->status !== 'done') {
            // Reset completion_date if status changes from done to something else
            $updateData['completion_date'] = null;
        }

        $service->update($updateData);

        return redirect()->back()->with('success', "Status servis {$service->customer_name} diupdate ke {$request->status}");
    }
}