@extends('layouts.app')

@section('title', 'Daftar Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-tools me-2"></i>Daftar Servis</h5>
    @if(in_array(auth()->user()->role, ['manajer', 'office']))
    <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Servis
    </a>
    @endif
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('services.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari pelanggan atau plat nomor..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="progress" {{ request('status') == 'progress' ? 'selected' : '' }}>On Progress</option>
                    <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-5 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                <a href="{{ route('services.index') }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle me-1"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th>Kendaraan</th>
                        <th>Tipe Servis</th>
                        <th>Status</th>
                        <th>Teknisi</th>
                        <th>Biaya</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $typeLabels = ['body_repair' => 'Body Repair', 'engine' => 'Mesin', 'electrical' => 'Elektrikal'];
                        $statusColors = ['pending' => 'warning', 'in_progress' => 'primary', 'progress' => 'primary', 'completed' => 'success', 'done' => 'success', 'cancelled' => 'danger'];
                        $statusLabels = ['pending' => 'Pending', 'in_progress' => 'On Progress', 'progress' => 'On Progress', 'completed' => 'Selesai', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                        $statusOptions = [
                            'pending' => ['label' => '⏳ Pending', 'color' => 'warning'],
                            'progress' => ['label' => '🔧 On Progress', 'color' => 'primary'],
                            'done' => ['label' => '✅ Selesai', 'color' => 'success'],
                            'cancelled' => ['label' => '❌ Dibatalkan', 'color' => 'danger']
                        ];
                    @endphp
                    @forelse($services as $service)
                    <tr>
                        <td>{{ $loop->iteration + ($services->currentPage() - 1) * $services->perPage() }}</td>
                        <td>
                            @if($service->vehicle && $service->vehicle->customer)
                                <a href="{{ route('customers.show', $service->vehicle->customer) }}" class="text-decoration-none">
                                    {{ $service->vehicle->customer->name }}
                                </a>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($service->vehicle)
                                <strong>{{ $service->vehicle->plate_number }}</strong>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            {{ $typeLabels[$service->type] ?? $service->type }}
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusColors[$service->status] ?? 'secondary' }}">
                                {{ $statusLabels[$service->status] ?? $service->status }}
                            </span>
                        </td>
                        <td>
                            @if($service->technicians->count() > 0)
                                @foreach($service->technicians as $tech)
                                    <span class="badge bg-info">{{ $tech->name }}</span>
                                @endforeach
                            @else
                                <span class="text-muted">Belum ditugaskan</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($service->total_cost ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                {{-- Detail Button (semua role) --}}
                                <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                
{{-- Update Status Button (teknisi - bisa update semua order) --}}
@if(in_array(auth()->user()->role, ['teknisi']))
<button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#statusModal{{ $service->id }}" title="Update Status">
    <i class="bi bi-check2-circle"></i>
</button>
                                @endif
                                
                                {{-- Edit & Delete (manajer/office only) --}}
                                @if(in_array(auth()->user()->role, ['manajer', 'office']))
                                <a href="{{ route('services.edit', $service) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus servis ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    
{{-- Modal Update Status untuk Teknisi --}}
@if(in_array(auth()->user()->role, ['teknisi']))
<tr>
    <td colspan="8">
        <div class="modal fade" id="statusModal{{ $service->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-check2-circle me-2"></i>Update Status Servis
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('services.update-status', $service) }}" method="POST">
                        @csrf @method('PATCH')
                        <div class="modal-body">
                            <div class="alert alert-info mb-3">
                                <strong>Servis:</strong> {{ $service->vehicle_plate }}<br>
                                <strong>Pelanggan:</strong> {{ $service->vehicle?->customer?->name ?? $service->customer_name }}<br>
                                <strong>Status Saat Ini:</strong> {{ $statusLabels[$service->status] ?? $service->status }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Status Baru:</label>
                                @foreach($statusOptions as $value => $option)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="status" value="{{ $value }}" id="status{{ $service->id }}{{ $value }}" {{ $service->status === $value ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status{{ $service->id }}{{ $value }}">
                                        {{ $option['label'] }}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            <div class="alert alert-warning mb-0">
                                <i class="bi bi-info-circle me-2"></i>
                                Status yang Anda update akan langsung terlihat di Dashboard Manajer/Office.
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-check-lg me-1"></i> Update Status
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </td>
</tr>
@endif
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="bi bi-tools"></i>
                                Belum ada data servis.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($services->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $services->links() }}
    </div>
    @endif
</div>
@endsection