@extends('layouts.app')

@section('title', 'Detail Spare Part')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-box me-2"></i>Detail Spare Part</h5>
    <div class="d-flex gap-2">
        @if(auth()->user()->role === 'manager')
        <a href="{{ route('spare-parts.edit', $sparePart) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Edit</a>
        @endif
        <a href="{{ route('spare-parts.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-info-circle me-2"></i>Informasi Spare Part</div>
            <div class="card-body">
                <table class="table table-sm table-borderless mb-0">
                    <tr><th class="text-muted w-30">Nama</th><td><strong>{{ $sparePart->name }}</strong></td></tr>
                    <tr><th class="text-muted">Stok</th>
                        <td>
                            @if($sparePart->stock <= 0)
                                <span class="badge bg-danger">Habis</span>
                            @elseif($sparePart->stock <= $sparePart->min_stock)
                                <span class="badge bg-warning text-dark">{{ $sparePart->stock }} pcs</span>
                            @else
                                <span class="badge bg-success">{{ $sparePart->stock }} pcs</span>
                            @endif
                        </td>
                    </tr>
                    <tr><th class="text-muted">Min. Stok</th><td>{{ $sparePart->min_stock }} pcs</td></tr>
                    <tr><th class="text-muted">Harga</th><td><strong>Rp {{ number_format($sparePart->price, 0, ',', '.') }}</strong></td></tr>
                    <tr><th class="text-muted">Deskripsi</th><td>{{ $sparePart->description ?? '<span class="text-muted">—</span>' }}</td></tr>
                    <tr><th class="text-muted">Dibuat</th><td>{{ $sparePart->created_at->format('d M Y H:i') }}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection