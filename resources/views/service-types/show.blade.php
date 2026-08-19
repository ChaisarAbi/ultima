@extends('layouts.app')

@section('title', 'Detail Type Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-tags me-2"></i>Detail Type Servis</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('service-types.edit', $serviceType) }}" class="btn btn-outline-warning btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('service-types.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Info Card -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-info-circle me-1"></i> Informasi Type Servis</span>
                <span class="badge {{ $serviceType->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                    {{ $serviceType->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td style="width: 200px;"><strong>Nama</strong></td>
                                <td>{{ $serviceType->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Slug</strong></td>
                                <td><code>{{ $serviceType->slug }}</code></td>
                            </tr>
                            <tr>
                                <td><strong>Deskripsi</strong></td>
                                <td>{{ $serviceType->description ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Base Price</strong></td>
                                <td>Rp {{ number_format($serviceType->base_price ?? 0, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Urutan Tampil</strong></td>
                                <td>{{ $serviceType->sort_order ?? 0 }}</td>
                            </tr>
                            <tr>
                                <td><strong>Dibuat</strong></td>
                                <td>{{ $serviceType->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td><strong>Terakhir Diupdate</strong></td>
                                <td>{{ $serviceType->updated_at->format('d M Y H:i') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Card -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="bi bi-bar-chart me-1"></i> Statistik
            </div>
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="stat-value">{{ $serviceType->services_count ?? 0 }}</div>
                    <div class="stat-label">Total Service Order</div>
                </div>
                @if(($serviceType->services_count ?? 0) > 0)
                <a href="{{ route('services.index', ['type' => $serviceType->slug]) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i> Lihat Service
                </a>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Actions --}}
<div class="card mt-4">
    <div class="card-body">
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('service-types.edit', $serviceType) }}" class="btn btn-warning btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit Type Servis
            </a>
            <form action="{{ route('service-types.destroy', $serviceType) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus type servis ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm">
                    <i class="bi bi-trash me-1"></i> Hapus Type Servis
                </button>
            </form>
        </div>
    </div>
</div>
@endsection