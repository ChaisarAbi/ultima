<?php

namespace App\Http\Controllers;

use App\Models\SparePart;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SparePartController extends Controller
{
    public function index()
    {
        $spareParts = SparePart::latest()->paginate(10);
        return view('spare-parts.index', compact('spareParts'));
    }

    public function create()
    {
        return view('spare-parts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'part_number' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $sparePart = SparePart::create($validated);

        ActivityLog::log('create', 'Menambahkan spare part baru: ' . $sparePart->name, $sparePart);

        return redirect()->route('spare-parts.index')
            ->with('success', 'Spare part berhasil ditambahkan.');
    }

    public function show(SparePart $sparePart)
    {
        $sparePart->load('services');
        return view('spare-parts.show', compact('sparePart'));
    }

    public function edit(SparePart $sparePart)
    {
        return view('spare-parts.edit', compact('sparePart'));
    }

    public function update(Request $request, SparePart $sparePart)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'part_number' => 'nullable|string|max:100',
            'stock' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $old = $sparePart->toArray();
        $sparePart->update($validated);

        ActivityLog::log('update', 'Mengupdate spare part: ' . $sparePart->name, $sparePart, $old, $sparePart->toArray());

        return redirect()->route('spare-parts.index')
            ->with('success', 'Spare part berhasil diupdate.');
    }

    public function destroy(SparePart $sparePart)
    {
        $name = $sparePart->name;
        $sparePart->delete();

        ActivityLog::log('delete', 'Menghapus spare part: ' . $name);

        return redirect()->route('spare-parts.index')
            ->with('success', 'Spare part berhasil dihapus.');
    }
}