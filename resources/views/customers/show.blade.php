@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-person me-2"></i>Detail Pelanggan</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.edit', $customer) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Informasi Pelanggan</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted w-25">Nama</th><td><strong>{{ $customer->name }}</strong></td></tr>
                    <tr><th class="text-muted">Telepon</th><td>{{ $customer->phone }}</td></tr>
                    <tr><th class="text-muted">Alamat</th><td>{{ $customer->address ?? '<span class="text-muted">—</span>' }}</td></tr>
                    <tr><th class="text-muted">Total Kendaraan</th><td><span class="badge bg-info">{{ $customer->vehicles->count() }}</span></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-car-front me-2"></i>Daftar Kendaraan</span>
                <a href="{{ route('vehicles.create') }}?customer_id={{ $customer->id }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Tambah Kendaraan</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Plat</th>
                                <th>Merek</th>
                                <th>Model</th>
                                <th>Tahun</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->vehicles as $vehicle)
                            <tr>
                                <td><strong>{{ $vehicle->plate_number }}</strong></td>
                                <td>{{ $vehicle->brand }}</td>
                                <td>{{ $vehicle->model }}</td>
                                <td>{{ $vehicle->year ?? '-' }}</td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">
                                    <div class="empty-state">
                                        <i class="bi bi-car-front"></i>
                                        Belum ada kendaraan.
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection