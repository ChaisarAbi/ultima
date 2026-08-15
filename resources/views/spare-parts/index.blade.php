@extends('layouts.app')

@section('title', 'Daftar Spare Part')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-box me-2"></i>Daftar Spare Part</h5>
    @if(auth()->user()->role === 'manager')
    <a href="{{ route('spare-parts.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg"></i> Tambah Spare Part
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
                        <th>Nama</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($spareParts as $sp)
                    <tr>
                        <td>{{ $loop->iteration + ($spareParts->currentPage() - 1) * $spareParts->perPage() }}</td>
                        <td><strong>{{ $sp->name }}</strong></td>
                        <td>
                            @if($sp->stock <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($sp->stock <= $sp->minimum_stock)
                                <span class="badge bg-warning text-dark">{{ $sp->stock }} pcs</span>
                            @else
                                <span class="badge bg-success">{{ $sp->stock }} pcs</span>
                            @endif
                        </td>
                        <td>Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <div class="d-flex gap-1 justify-content-center">
                                <a href="{{ route('spare-parts.show', $sp) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(auth()->user()->role === 'manager')
                                <a href="{{ route('spare-parts.edit', $sp) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('spare-parts.destroy', $sp) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus spare part {{ $sp->name }}?')">
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
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="bi bi-box"></i>
                                Belum ada data spare part.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($spareParts->hasPages())
    <div class="card-footer d-flex justify-content-center">
        {{ $spareParts->links() }}
    </div>
    @endif
</div>
@endsection