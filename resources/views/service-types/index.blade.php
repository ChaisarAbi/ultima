@extends('layouts.app')

@section('title', 'Daftar Type Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-tags me-2"></i>Daftar Type Servis</h5>
    @if(in_array(auth()->user()->role, ['manajer', 'office']))
    <a href="{{ route('service-types.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Type Servis
    </a>
    @endif
</div>

{{-- Search & Filter --}}
<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('service-types.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Cari nama atau deskripsi..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                <a href="{{ route('service-types.index') }}" class="btn btn-secondary">
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
                        <th>Nama</th>
                        <th>Slug</th>
                        <th>Base Price</th>
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($serviceTypes as $type)
                    <tr>
                        <td>{{ $loop->iteration + ($serviceTypes->currentPage() - 1) * $serviceTypes->perPage() }}</td>
                        <td><strong>{{ $type->name }}</strong></td>
                        <td><code>{{ $type->slug }}</code></td>
                        <td>Rp {{ number_format($type->base_price ?? 0, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge {{ $type->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ $type->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('service-types.show', $type) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array(auth()->user()->role, ['manajer', 'office']))
                                <a href="{{ route('service-types.edit', $type) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('service-types.destroy', $type) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus type servis {{ $type->name }}?')">
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
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-tags"></i>
                                Belum ada data type servis.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($serviceTypes->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $serviceTypes->links() }}
    </div>
    @endif
</div>
@endsection