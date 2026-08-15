@extends('layouts.app')

@section('title', 'Tambah Servis')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-plus-circle me-2"></i>Tambah Servis Baru</h5>
    <a href="{{ route('services.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kendaraan <span class="text-danger">*</span></label>
                    <select name="vehicle_id" class="form-select @error('vehicle_id') is-invalid @enderror" required>
                        <option value="">— Pilih Kendaraan —</option>
                        @foreach($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id') == $vehicle->id ? 'selected' : '' }}>
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
                        <option value="body_repair" {{ old('type') == 'body_repair' ? 'selected' : '' }}>Body Repair</option>
                        <option value="engine" {{ old('type') == 'engine' ? 'selected' : '' }}>Mesin</option>
                        <option value="electrical" {{ old('type') == 'electrical' ? 'selected' : '' }}>Elektrikal</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Masuk <span class="text-danger">*</span></label>
                    <input type="date" name="entry_date" class="form-control @error('entry_date') is-invalid @enderror" value="{{ old('entry_date', date('Y-m-d')) }}" required>
                    @error('entry_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal Selesai</label>
                    <input type="date" name="completion_date" class="form-control @error('completion_date') is-invalid @enderror" value="{{ old('completion_date') }}">
                    <small class="text-muted">Kosongkan jika belum selesai</small>
                    @error('completion_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teknisi</label>
                    <select name="technicians[]" class="form-select @error('technicians') is-invalid @enderror" multiple>
                        @foreach($technicians as $tech)
                            <option value="{{ $tech->id }}" {{ in_array($tech->id, old('technicians', [])) ? 'selected' : '' }}>
                                {{ $tech->name }}
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Gunakan Ctrl/Cmd+Click untuk pilih lebih dari satu</small>
                    @error('technicians') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection