@extends('layouts.app')

@section('title', 'Detail Kendaraan')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-truck me-2"></i>Detail Kendaraan</h5>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'manager')
        <a href="{{ route('vehicles.edit', $vehicle) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Kendaraan</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted w-30">Plat Nomor</th><td><strong>{{ $vehicle->plate_number }}</strong></td></tr>
                    <tr><th class="text-muted">Merek</th><td>{{ $vehicle->brand }}</td></tr>
                    <tr><th class="text-muted">Model</th><td>{{ $vehicle->model }}</td></tr>
                    <tr><th class="text-muted">Tahun</th><td>{{ $vehicle->year ?? '<span class="text-muted">—</span>' }}</td></tr>
                    <tr><th class="text-muted">Warna</th><td>{{ $vehicle->color ?? '<span class="text-muted">—</span>' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Pemilik</div>
            <div class="card-body">
                @if($vehicle->customer)
                    <table class="table table-sm table-borderless mb-0">
                        <tr><th class="text-muted w-30">Nama</th><td><strong>{{ $vehicle->customer->name }}</strong></td></tr>
                        <tr><th class="text-muted">Telepon</th><td>{{ $vehicle->customer->phone }}</td></tr>
                        <tr><th class="text-muted">Email</th><td>{{ $vehicle->customer->email ?? '<span class="text-muted">—</span>' }}</td></tr>
                        <tr><th class="text-muted">Alamat</th><td>{{ $vehicle->customer->address ?? '<span class="text-muted">—</span>' }}</td></tr>
                    </table>
                    <a href="{{ route('customers.show', $vehicle->customer) }}" class="btn btn-outline-primary btn-sm mt-3">
                        <i class="bi bi-person"></i> Lihat Detail Pelanggan
                    </a>
                @else
                    <p class="text-muted mb-0">Tidak ada data pelanggan.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-12">
        <div class="card">
            <div class="card-header"><i class="bi bi-tools me-2"></i>Riwayat Servis</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Jenis Servis</th>
                                <th>Status</th>
                                <th>Biaya</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($vehicle->services->sortByDesc('created_at') as $svc)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $svc->created_at->format('d M Y') }}</td>
                                <td>{{ $svc->service_type }}</td>
                                <td>
                                    @php
                                        $sc = ['pending' => 'warning', 'in_progress' => 'primary', 'completed' => 'success', 'cancelled' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $sc[$svc->status] ?? 'secondary' }}">{{ $svc->status_label }}</span>
                                </td>
                                <td>Rp {{ number_format($svc->total_cost ?? $svc->estimated_cost, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('services.show', $svc) }}" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i></a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="bi bi-tools"></i>
                                        Belum ada riwayat servis.
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