@extends('layouts.app')

@section('title', 'Monitoring')

@push('styles')
<style>
.tabs { display: flex; gap: 4px; margin-bottom: 1.25rem; background: #fff; border-radius: var(--radius); padding: 4px; box-shadow: var(--card-shadow); border: 1px solid rgba(0,0,0,0.04); }
.tab-btn { padding: .5rem 1.25rem; border: none; background: transparent; border-radius: var(--radius-sm); cursor: pointer; font-size: .8125rem; font-weight: 500; color: #64748b; transition: all .2s; font-family: inherit; }
.tab-btn.active { background: var(--accent); color: #fff; }
.tab-btn:hover:not(.active) { background: #f1f5f9; }
.tab-content { display: none; }
.tab-content.active { display: block; }

.low-stock-warning { background: #fef2f2; border: 1px solid #fecaca; border-radius: var(--radius-sm); padding: .75rem 1rem; margin-bottom: 1rem; color: #991b1b; font-size: .8125rem; font-weight: 500; }

.modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.3); backdrop-filter: blur(4px); z-index: 1050; align-items: center; justify-content: center; }
.modal-overlay.show { display: flex; }
.modal { background: #fff; border-radius: var(--radius); padding: 1.5rem; max-width: 400px; width: 90%; box-shadow: var(--card-shadow-hover); }
.modal h3 { font-size: 1rem; margin-bottom: 1rem; font-weight: 700; color: #0f172a; }
.modal input, .modal select { width: 100%; padding: .5rem .75rem; border-radius: var(--radius-sm); border: 1px solid #e2e8f0; margin-bottom: .75rem; font-size: .8125rem; font-family: inherit; }
.modal input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(37,99,235,0.1); outline: none; }

.badge-low { background: #fee2e2; color: #991b1b; }
.badge-ok { background: #d1fae5; color: #065f46; }
</style>
@endpush

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-graph-up me-2"></i>BMW ULTIMA</h5>
</div>

@if($lowStockCount > 0)
<div class="low-stock-warning">⚠️ Terdapat <strong>{{ $lowStockCount }}</strong> spare part dengan stok kritis!</div>
@endif

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-wrench"></i></div>
            <div class="stat-indicator" style="background:#3b82f6;"></div>
            <div class="stat-label">Servis Hari Ini</div>
            <div class="stat-value">{{ $todayServices }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-check-circle"></i></div>
            <div class="stat-indicator" style="background:#10b981;"></div>
            <div class="stat-label">Selesai Hari Ini</div>
            <div class="stat-value">{{ $todayCompleted }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-indicator" style="background:#f59e0b;"></div>
            <div class="stat-label">Revenue Hari Ini</div>
            <div class="stat-value">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-grid-3x3-gap"></i></div>
            <div class="stat-indicator" style="background:#7c3aed;"></div>
            <div class="stat-label">Active Tab</div>
            <div class="stat-value" id="activeTabLabel">Services</div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="tabs">
    <button class="tab-btn active" onclick="switchTab('services')">📋 Services</button>
    <button class="tab-btn" onclick="switchTab('spareparts')">🔧 Spare Parts</button>
</div>

<!-- Tab: Services -->
<div class="tab-content active" id="tab-services">
    <div class="card">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <span>Daftar Servis</span>
        </div>
        <div class="card-body p-0">
            <form method="GET" action="{{ route('monitoring') }}" class="p-3 pb-0 d-flex flex-wrap gap-2">
                <select name="filter" class="form-select" style="width:auto;" onchange="this.form.submit()">
                    <option value="all" {{ $filter == 'all' ? 'selected' : '' }}>Semua Status</option>
                    <option value="pending" {{ $filter == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="progress" {{ $filter == 'progress' ? 'selected' : '' }}>Progress</option>
                    <option value="done" {{ $filter == 'done' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $filter == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <input type="text" name="search" placeholder="Cari pelanggan/plat..." value="{{ $search }}" class="form-control" style="flex:1;min-width:180px;">
                <button type="submit" class="btn btn-primary">Cari</button>
            </form>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Pelanggan</th>
                            <th>Plat</th>
                            <th>Tipe</th>
                            <th>Masuk</th>
                            <th>Selesai</th>
                            <th>Status</th>
                            <th>Biaya</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $s)
                        <tr>
                            <td><strong>{{ $s->customer_name }}</strong></td>
                            <td>{{ $s->vehicle_plate }}</td>
                            <td>{{ str_replace('_', ' ', ucfirst($s->type)) }}</td>
                            <td>{{ $s->entry_date ? \Carbon\Carbon::parse($s->entry_date)->format('d/m/Y H:i') : '-' }}</td>
                            <td>{{ $s->completion_date ? \Carbon\Carbon::parse($s->completion_date)->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                @php
                                    $badgeClass = match($s->status) {
                                        'pending' => 'bg-warning text-dark',
                                        'progress' => 'bg-primary',
                                        'done' => 'bg-success',
                                        'cancelled' => 'bg-danger',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ ucfirst($s->status) }}</span>
                            </td>
                            <td>Rp {{ number_format($s->total_cost, 0, ',', '.') }}</td>
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    @if($s->status == 'pending')
                                    <form action="{{ route('service.update-status', $s) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="progress">
                                        <button class="btn btn-sm btn-primary" title="Mulai"><i class="bi bi-play-fill"></i></button>
                                    </form>
                                    @endif
                                    @if($s->status == 'progress')
                                    <form action="{{ route('service.update-status', $s) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="done">
                                        <button class="btn btn-sm btn-success" title="Selesai"><i class="bi bi-check-lg"></i></button>
                                    </form>
                                    @endif
                                    @if($s->status == 'pending' || $s->status == 'progress')
                                    <form action="{{ route('service.update-status', $s) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan servis ini?')">
                                        @csrf
                                        <input type="hidden" name="status" value="cancelled">
                                        <button class="btn btn-sm btn-danger" title="Batal"><i class="bi bi-x-lg"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8"><div class="empty-state"><i class="bi bi-inbox"></i>Tidak ada data servis</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(method_exists($services, 'links'))
        <div class="card-footer d-flex justify-content-center">
            {{ $services->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Tab: Spare Parts -->
<div class="tab-content" id="tab-spareparts">
    <div class="card">
        <div class="card-header">
            <span>Manajemen Spare Part</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Stok</th>
                            <th>Min. Stok</th>
                            <th>Harga</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($spareParts as $sp)
                        <tr>
                            <td><strong>{{ $sp->name }}</strong></td>
                            <td>{{ $sp->stock }}</td>
                            <td>{{ $sp->minimum_stock }}</td>
                            <td>Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $sp->stock <= $sp->minimum_stock ? 'badge-low' : 'badge-ok' }}">
                                    {{ $sp->stock <= $sp->minimum_stock ? 'Stok Kritis' : 'Tersedia' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="openStockModal({{ $sp->id }}, '{{ $sp->name }}', {{ $sp->stock }}, {{ $sp->minimum_stock }})">
                                    <i class="bi bi-pencil"></i> Update
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6"><div class="empty-state"><i class="bi bi-box"></i>Belum ada spare part</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Stock Modal -->
<div class="modal-overlay" id="stockModal">
    <div class="modal">
        <h3>Update Stock: <span id="modalPartName"></span></h3>
        <form id="stockForm" method="POST">
            @csrf
            <label style="font-size:.75rem;color:#64748b;margin-bottom:4px;display:block;font-weight:600;">Stok Saat Ini</label>
            <input type="number" name="stock" id="modalStock" min="0" required>
            <label style="font-size:.75rem;color:#64748b;margin-bottom:4px;display:block;font-weight:600;">Minimum Stok</label>
            <input type="number" name="minimum_stock" id="modalMinStock" min="0">
            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary flex-fill">Simpan</button>
                <button type="button" class="btn btn-outline-secondary flex-fill" onclick="closeStockModal()">Batal</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
    document.getElementById('activeTabLabel').textContent = tab === 'services' ? 'Services' : 'Spare Parts';
}

function openStockModal(id, name, stock, minStock) {
    document.getElementById('stockModal').classList.add('show');
    document.getElementById('modalPartName').textContent = name;
    document.getElementById('modalStock').value = stock;
    document.getElementById('modalMinStock').value = minStock;
    document.getElementById('stockForm').action = '/monitoring/spare-part/' + id + '/stock';
}

function closeStockModal() {
    document.getElementById('stockModal').classList.remove('show');
}

document.getElementById('stockModal')?.addEventListener('click', function(e) {
    if (e.target === this) closeStockModal();
});
</script>
@endpush