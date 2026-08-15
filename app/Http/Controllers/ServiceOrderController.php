<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\ServiceOrder;
use App\Models\SparePart;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with(['vehicle.customer', 'technicians']);

        if ($request->filled('status')) {
            $query->ofStatus($request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('vehicle_plate', 'like', "%{$search}%");
            });
        }

        $services = $query->latest()->paginate(10);
        return view('services.index', compact('services'));
    }

    public function create()
    {
        $vehicles = Vehicle::with('customer')->get();
        $technicians = User::where('role', 'teknisi')->get();
        $spareParts = SparePart::where('stock', '>', 0)->get();
        return view('services.create', compact('vehicles', 'technicians', 'spareParts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:body_repair,engine,electrical',
            'entry_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:entry_date',
            'technicians' => 'nullable|array',
            'technicians.*' => 'exists:users,id',
        ]);

        $vehicle = Vehicle::with('customer')->findOrFail($validated['vehicle_id']);

        $service = Service::create([
            'vehicle_id' => $vehicle->id,
            'customer_name' => $vehicle->customer->name,
            'vehicle_plate' => $vehicle->plate_number,
            'type' => $validated['type'],
            'entry_date' => $validated['entry_date'],
            'completion_date' => $validated['completion_date'] ?? null,
            'status' => 'pending',
            'total_cost' => 0,
        ]);

        if (!empty($validated['technicians'])) {
            $service->technicians()->attach($validated['technicians']);
        }

        ActivityLog::log('create', 'Membuat order servis baru: ' . $service->vehicle_plate, $service);

        return redirect()->route('services.index')
            ->with('success', 'Order servis berhasil dibuat.');
    }

    public function show(Service $service)
    {
        $service->load(['vehicle.customer', 'spareParts', 'technicians']);
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        $vehicles = Vehicle::with('customer')->get();
        $technicians = User::where('role', 'teknisi')->get();
        $spareParts = SparePart::where('stock', '>', 0)->get();
        return view('services.edit', compact('service', 'vehicles', 'technicians', 'spareParts'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|in:body_repair,engine,electrical',
            'entry_date' => 'required|date',
            'completion_date' => 'nullable|date|after_or_equal:entry_date',
            'status' => 'required|in:pending,progress,done,cancelled',
            'total_cost' => 'nullable|numeric|min:0',
            'technicians' => 'nullable|array',
            'technicians.*' => 'exists:users,id',
        ]);

        $vehicle = Vehicle::with('customer')->findOrFail($validated['vehicle_id']);

        $old = $service->toArray();
        $service->update([
            'vehicle_id' => $vehicle->id,
            'customer_name' => $vehicle->customer->name,
            'vehicle_plate' => $vehicle->plate_number,
            'type' => $validated['type'],
            'entry_date' => $validated['entry_date'],
            'completion_date' => $validated['completion_date'],
            'status' => $validated['status'],
            'total_cost' => $validated['total_cost'] ?? 0,
        ]);

        if (isset($validated['technicians'])) {
            $service->technicians()->sync($validated['technicians']);
        }

        ActivityLog::log('update', 'Mengupdate order servis: ' . $service->vehicle_plate, $service, $old, $service->toArray());

        return redirect()->route('services.index')
            ->with('success', 'Order servis berhasil diupdate.');
    }

    public function destroy(Service $service)
    {
        $plate = $service->vehicle_plate;
        $service->technicians()->detach();
        $service->spareParts()->detach();
        $service->delete();

        ActivityLog::log('delete', 'Menghapus order servis: ' . $plate);

        return redirect()->route('services.index')
            ->with('success', 'Order servis berhasil dihapus.');
    }

    public function addSparePart(Request $request, Service $service)
    {
        $validated = $request->validate([
            'spare_part_id' => 'required|exists:spare_parts,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $sparePart = SparePart::findOrFail($validated['spare_part_id']);

        if ($sparePart->stock < $validated['quantity']) {
            return back()->withErrors(['spare_part_id' => 'Stok tidak mencukupi.']);
        }

        $service->spareParts()->attach($validated['spare_part_id'], [
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
        ]);

        $sparePart->decrement('stock', $validated['quantity']);

        $service->increment('total_cost', $validated['price'] * $validated['quantity']);

        ActivityLog::log('add_spare_part', 'Menambahkan spare part ke servis: ' . $sparePart->name, $service);

        return back()->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function updateStatus(Request $request, Service $service)
    {
        // Allow technicians (all) OR manajer/office to update status
        $user = auth()->user();
        $isTechnician = $user->role === 'teknisi';
        $isAdmin = in_array($user->role, ['manajer', 'office']);
        
        if (!$isTechnician && !$isAdmin) {
            abort(403, 'Anda tidak memiliki izin untuk update status servis ini.');
        }

        $validated = $request->validate([
            'status' => 'required|in:pending,progress,done,cancelled',
        ]);

        $old = $service->toArray();
        $data = ['status' => $validated['status']];

        if ($validated['status'] === 'done') {
            $data['completion_date'] = now();
        }

        $service->update($data);

        $roleLabel = $isTechnician ? 'Teknisi' : ucfirst($user->role);
        $actionText = $roleLabel . ' mengupdate status servis menjadi: ' . $validated['status'];
        
        ActivityLog::log('update_status', $actionText, $service, $old, $service->toArray());

        return back()->with('success', 'Status servis berhasil diupdate.');
    }
}