@extends('layouts.app')

@section('title', 'Detail Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-tools me-2"></i>Detail Servis</h5>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'manager')
        <a href="{{ route('services.edit', $service) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Servis</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    @php
                        $typeLabels = ['body_repair' => 'Body Repair', 'engine' => 'Mesin', 'electrical' => 'Elektrikal'];
                        $statusColors = ['pending' => 'warning', 'progress' => 'primary', 'done' => 'success', 'cancelled' => 'danger'];
                        $statusLabels = ['pending' => 'Pending', 'progress' => 'Proses', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                    @endphp
                    <tr><th class="text-muted w-30">Tipe Servis</th><td><strong>{{ $typeLabels[$service->type] ?? $service->type }}</strong></td></tr>
                    <tr><th class="text-muted">Status</th>
                        <td>
                            <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                {{ $statusLabels[$service->status] ?? $service->status }}
                            </span>
                        </td>
                    </tr>
                    <tr><th class="text-muted">Tanggal Masuk</th><td>{{ $service->entry_date ? $service->entry_date->format('d M Y') : '-' }}</td></tr>
                    <tr><th class="text-muted">Tanggal Selesai</th><td>{{ $service->completion_date ? $service->completion_date->format('d M Y') : '-' }}</td></tr>
                    <tr><th class="text-muted">Total Biaya</th><td><strong>Rp {{ number_format($service->total_cost ?? 0, 0, ',', '.') }}</strong></td></tr>
                    <tr><th class="text-muted">Dibuat</th><td>{{ $service->created_at->format('d M Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-person me-2"></i>Informasi Kendaraan & Pelanggan</div>
            <div class="card-body">
                @if($service->vehicle)
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted w-30">Plat</th><td><strong>{{ $service->vehicle->plate_number }}</strong></td></tr>
                    <tr><th class="text-muted">Merek</th><td>{{ $service->vehicle->brand }}</td></tr>
                    <tr><th class="text-muted">Model</th><td>{{ $service->vehicle->model }}</td></tr>
                    <tr><th class="text-muted">Pelanggan</th>
                        <td>
                            @if($service->vehicle->customer)
                                <a href="{{ route('customers.show', $service->vehicle->customer) }}" class="text-decoration-none">
                                    {{ $service->vehicle->customer->name }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                </table>
                @else
                <p class="text-muted mb-0">Tidak ada data kendaraan.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-people me-2"></i>Teknisi</div>
            <div class="card-body">
                @if($service->technicians->count() > 0)
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($service->technicians as $tech)
                            <span class="badge bg-info p-2">{{ $tech->name }}</span>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted mb-0">Belum ada teknisi ditugaskan.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru</div>
            <div class="card-body">
                @php
                    $logs = \App\Models\ActivityLog::where('model_type', \App\Models\Service::class)
                        ->where('model_id', $service->id)
                        ->orderBy('created_at', 'desc')
                        ->take(5)
                        ->get();
                @endphp
                @forelse($logs as $log)
                    <div class="activity-item">
                        <div><strong>{{ $log->user->name ?? 'System' }}</strong> {{ $log->action }}</div>
                        <div class="time">{{ $log->created_at->diffForHumans() }}</div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada aktivitas.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection