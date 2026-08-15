@extends('layouts.app')

@section('title', 'Dashboard Teknisi')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-person-workspace me-2"></i>Dashboard Teknisi</h5>
    <span class="badge bg-primary fs-6 px-3 py-2">{{ auth()->user()->name }}</span>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Ditugaskan</div>
            <div class="stat-value text-primary">{{ $assignedServices->count() }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">On Progress</div>
            <div class="stat-value text-warning">{{ $pendingServices }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-label">Selesai</div>
            <div class="stat-value text-success">{{ $completedServices }}</div>
        </div>
    </div>
</div>

<div class="card mt-3">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Servis Saya</h5>
        <small class="text-muted">Status sinkron dengan Dashboard Manajer/Office</small>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Jenis Servis</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typeLabels = ['body_repair' => 'Body Repair', 'engine' => 'Mesin', 'electrical' => 'Elektrikal'];
                        // Standardize status: database uses pending/progress/done/cancelled
                        $statusColors = ['pending' => 'warning', 'progress' => 'primary', 'done' => 'success', 'cancelled' => 'danger'];
                        $statusLabels = ['pending' => 'Pending', 'progress' => 'On Progress', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                    @endphp
                    @forelse($assignedServices as $service)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $service->vehicle?->customer?->name ?? $service->customer_name ?? '—' }}</td>
                        <td><strong>{{ $service->vehicle?->plate_number ?? $service->vehicle_plate ?? '—' }}</strong></td>
                        <td>{{ $typeLabels[$service->type] ?? $service->type }}</td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                {{ $statusLabels[$service->status] ?? $service->status }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-tools"></i>
                                Belum ada servis yang ditugaskan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Info Box --}}
<div class="alert alert-info mt-3 d-flex align-items-center">
    <i class="bi bi-info-circle me-2 fs-4"></i>
    <div>
        <strong>Info:</strong> Status servis yang Anda update akan langsung terlihat di Dashboard Manajer/Office.
        Pastikan untuk mengubah status menjadi "Selesai" setelah pengerjaan selesai.
    </div>
</div>
@endsection