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
            <div class="stat-label">Sedang Dikerjakan</div>
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

<div class="card">
    <div class="card-header"><i class="bi bi-list-check me-2"></i>Servis Saya</div>
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
                        $statusColors = ['pending' => 'warning', 'progress' => 'primary', 'done' => 'success', 'cancelled' => 'danger'];
                        $statusLabels = ['pending' => 'Pending', 'progress' => 'Proses', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
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
@endsection