@extends('layouts.app')

@section('title', 'Edit Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-pencil me-2"></i>Edit Servis</h5>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('services.update', $service) }}" method="POST">
            @csrf @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                    <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                        <option value="">— Pilih Kendaraan —</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $service->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                {{ $vehicle->plate_number }} — {{ $vehicle->brand }} {{ $vehicle->model }}
                                @if($vehicle->customer) ({{ $vehicle->customer->name }}) @endif
                            </option>
                        @endforeach
                    </select>
                    @error('vehicle_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe Servis <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="">— Pilih Tipe —</option>
                        <option value="body_repair" {{ old('type', $service->type) == 'body_repair' ? 'selected' : '' }}>Body Repair</option>
                        <option value="engine" {{ old('type', $service->type) == 'engine' ? 'selected' : '' }}>Mesin</option>
                        <option value="electrical" {{ old('type', $service->type) == 'electrical' ? 'selected' : '' }}>Elektrikal</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror">
                        <option value="pending" {{ old('status', $service->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="progress" {{ old('status', $service->status) == 'progress' ? 'selected' : '' }}>Proses</option>
                        <option value="done" {{ old('status', $service->status) == 'done' ? 'selected' : '' }}>Selesai</option>
                        <option value="cancelled" {{ old('status', $service->status) == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                    <input type="date" name="entry_date" class="form-control @error('entry_date') is-invalid @enderror" value="{{ old('entry_date', $service->entry_date ? $service->entry_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="completion_date" class="form-control @error('completion_date') is-invalid @enderror" value="{{ old('completion_date', $service->completion_date ? $service->completion_date->format('Y-m-d') : '') }}">
                    @error('completion_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Total Biaya</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" name="total_cost" class="form-control @error('total_cost') is-invalid @enderror" value="{{ old('total_cost', $service->total_cost) }}" min="0">
                        @error('total_cost') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teknisi</label>
                    <select name="technicians[]" class="form-select @error('technicians') is-invalid @enderror" multiple>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ in_array($tech->id, old('technicians', $service->technicians->pluck('id')->toArray())) ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Gunakan Ctrl/Cmd+Click untuk pilih lebih dari satu</small>
                    @error('technicians') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Update</button>
                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection