<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Vehicle;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with('customer')->latest()->paginate(10);
        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        return view('vehicles.create', compact('customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plate_number' => 'required|string|max:20|unique:vehicles',
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $vehicle = Vehicle::create($validated);

        ActivityLog::log('create', 'Menambahkan kendaraan baru: ' . $vehicle->plate_number, $vehicle);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil ditambahkan.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['customer', 'services' => fn($q) => $q->latest()]);
        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $customers = Customer::orderBy('name')->get();
        return view('vehicles.edit', compact('vehicle', 'customers'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'plate_number' => 'required|string|max:20|unique:vehicles,plate_number,' . $vehicle->id,
            'brand' => 'required|string|max:50',
            'model' => 'required|string|max:50',
            'year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
        ]);

        $old = $vehicle->toArray();
        $vehicle->update($validated);

        ActivityLog::log('update', 'Mengupdate data kendaraan: ' . $vehicle->plate_number, $vehicle, $old, $vehicle->toArray());

        return redirect()->route('vehicles.index')
            ->with('success', 'Data kendaraan berhasil diupdate.');
    }

    public function destroy(Vehicle $vehicle)
    {
        $plate = $vehicle->plate_number;
        $vehicle->delete();

        ActivityLog::log('delete', 'Menghapus kendaraan: ' . $plate);

        return redirect()->route('vehicles.index')
            ->with('success', 'Kendaraan berhasil dihapus.');
    }
}