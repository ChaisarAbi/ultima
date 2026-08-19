<?php

namespace App\Http\Controllers;

use App\Models\ServiceType;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $serviceTypes = $query->sorted()->paginate(10)->withQueryString();
        return view('service-types.index', compact('serviceTypes'));
    }

    public function create()
    {
        return view('service-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name',
            'slug' => 'required|string|max:255|unique:service_types,slug',
            'description' => 'nullable|string',
            'base_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $serviceType = ServiceType::create($validated);

        ActivityLog::log('create', 'Menambahkan tipe servis baru: ' . $serviceType->name, $serviceType);

        return redirect()->route('service-types.index')
            ->with('success', 'Tipe servis berhasil ditambahkan.');
    }

    public function show(ServiceType $serviceType)
    {
        $serviceType->loadCount('services');
        return view('service-types.show', compact('serviceType'));
    }

    public function edit(ServiceType $serviceType)
    {
        return view('service-types.edit', compact('serviceType'));
    }

    public function update(Request $request, ServiceType $serviceType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:service_types,name,' . $serviceType->id,
            'slug' => 'required|string|max:255|unique:service_types,slug,' . $serviceType->id,
            'description' => 'nullable|string',
            'base_price' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $old = $serviceType->toArray();
        $serviceType->update($validated);

        ActivityLog::log('update', 'Mengupdate tipe servis: ' . $serviceType->name, $serviceType, $old, $serviceType->toArray());

        return redirect()->route('service-types.index')
            ->with('success', 'Tipe servis berhasil diupdate.');
    }

    public function destroy(ServiceType $serviceType)
    {
        // Cek apakah tipe servis masih digunakan di service orders
        if ($serviceType->services()->count() > 0) {
            return back()->withErrors(['delete' => 'Tipe servis masih digunakan dalam order servis. Tidak dapat menghapus.']);
        }

        $name = $serviceType->name;
        $serviceType->delete();

        ActivityLog::log('delete', 'Menghapus tipe servis: ' . $name);

        return redirect()->route('service-types.index')
            ->with('success', 'Tipe servis berhasil dihapus.');
    }
}