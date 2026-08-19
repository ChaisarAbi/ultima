@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-people me-2"></i>Daftar Pelanggan</h5>
    <div class="d-flex gap-2">
        <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah Pelanggan
        </a>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal">
            <i class="bi bi-person-plus"></i> Quick Add
        </button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form action="{{ route('customers.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-8">
                <input type="text" name="search" class="form-control" placeholder="Cari nama, no. telepon, atau alamat..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Cari
                </button>
                <a href="{{ route('customers.index') }}" class="btn btn-secondary">
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
                        <th>Telepon</th>
                        <th>Alamat</th>
                        <th>Kendaraan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr>
                        <td>{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                        <td><strong>{{ $customer->name }}</strong></td>
                        <td>{{ $customer->phone }}</td>
                        <td>{{ $customer->address ?? '<span class="text-muted">—</span>' }}</td>
                        <td><span class="badge bg-info">{{ $customer->vehicles_count }} kendaraan</span></td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('customers.show', $customer) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('customers.destroy', $customer) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pelanggan {{ $customer->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="bi bi-people"></i>
                                Belum ada data pelanggan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($customers->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection