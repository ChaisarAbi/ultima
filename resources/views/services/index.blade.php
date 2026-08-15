@extends('layouts.app')

@section('title', 'Daftar Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-tools me-2"></i>Daftar Servis</h5>
    @if(auth()->user()->role === 'manager')
    <a href="{{ route('services.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Servis
    </a>
    @endif
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
                            @php
                                $typeLabels = ['body_repair' => 'Body Repair', 'engine' => 'Mesin', 'electrical' => 'Elektrikal'];
                            @endphp
                            {{ $typeLabels[$service->type] ?? $service->type }}
                        </td>
                        <td>
                            @php
                                $statusColors = ['pending' => 'warning', 'progress' => 'primary', 'done' => 'success', 'cancelled' => 'danger'];
                                $statusLabels = ['pending' => 'Pending', 'progress' => 'Proses', 'done' => 'Selesai', 'cancelled' => 'Dibatalkan'];
                            @endphp
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
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($service->total_cost ?? 0, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('services.show', $service) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'manager')
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