@extends('layouts.app')

@section('title', 'Tambah Kendaraan')

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-plus-circle me-2"></i>Tambah Kendaraan Baru</h5>
    <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('vehicles.store') }}" method="POST" id="vehicleForm">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Plat Nomor <span class="text-danger">*</span></label>
                    <input type="text" name="plate_number" class="form-control @error('plate_number') is-invalid @enderror" value="{{ old('plate_number') }}" required>
                    @error('plate_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Merek <span class="text-danger">*</span></label>
                    <input type="text" name="brand" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" required>
                    @error('brand') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Model <span class="text-danger">*</span></label>
                    <input type="text" name="model" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" required>
                    @error('model') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="year" class="form-control @error('year') is-invalid @enderror" value="{{ old('year') }}" min="1990" max="{{ date('Y') + 1 }}">
                    @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Warna</label>
                    <input type="text" name="color" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}">
                    @error('color') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <select name="customer_id" id="customerSelect" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">— Pilih Pelanggan —</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} ({{ $customer->phone }})
                                </option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#quickAddCustomerModal">
                            <i class="bi bi-person-plus"></i>
                        </button>
                    </div>
                    @error('customer_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                <a href="{{ route('vehicles.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
</div>

{{-- Quick Add Customer Modal --}}
<div class="modal fade" id="quickAddCustomerModal" tabindex="-1" aria-labelledby="quickAddCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickAddCustomerModalLabel"><i class="bi bi-person-plus me-2"></i>Tambah Pelanggan Cepat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="quickAddCustomerForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="quickName" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quickName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="quickPhone" class="form-label">No. Telepon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="quickPhone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="quickEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="quickEmail" name="email">
                    </div>
                    <div class="mb-3">
                        <label for="quickAddress" class="form-label">Alamat</label>
                        <textarea class="form-control" id="quickAddress" name="address" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Simpan & Pilih</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Quick Add Customer Form Submission
document.getElementById('quickAddCustomerForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const modal = new bootstrap.Modal(document.getElementById('quickAddCustomerModal'));
    
    fetch('{{ route("customers.store") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.id) {
            // Add new customer to select
            const select = document.getElementById('customerSelect');
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = `${data.name} (${data.phone})`;
            option.selected = true;
            select.value = data.id;
            
            // Close modal
            modal.hide();
            
            // Reset form
            this.reset();
            
            // Show success message
            alert('Pelanggan berhasil ditambahkan!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    });
});
</script>
@endpush
@endsection