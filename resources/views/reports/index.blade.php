@extends('layouts.app')

@section('title', 'Laporan')

@push('styles')
<style>
.archive-list { display: grid; gap: 12px; }
.archive-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1rem 1.25rem; background: #f8fafc; border-radius: var(--radius-sm);
    border: 1px solid #e2e8f0; transition: all 0.2s;
}
.archive-item:hover { background: #f1f5f9; }
.archive-item .info { flex: 1; }
.archive-item .title { font-weight: 600; font-size: .875rem; }
.archive-item .meta { font-size: .75rem; color: #94a3b8; margin-top: 2px; }
.badge-blue { background: #dbeafe; color: #1e40af; }
.badge-green { background: #d1fae5; color: #065f46; }
.badge-purple { background: #ede9fe; color: #6d28d9; }
.charts-row-report { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }
@media (max-width: 768px) { .charts-row-report { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan</h5>
</div>

{{-- Generate PDF --}}
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-file-earmark-pdf me-2"></i>Generate Laporan PDF</div>
    <div class="card-body">
        <form action="{{ route('reports.generate') }}" method="POST" class="d-flex flex-wrap gap-3 align-items-end">
            @csrf
            <div>
                <label class="form-label">Periode</label>
                <select name="period" class="form-select">
                    <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Bulanan</option>
                    <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Tahunan</option>
                </select>
            </div>
            <div>
                <label class="form-label">Bulan</label>
                <select name="month" class="form-select">
                    @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="form-label">Tahun</label>
                <select name="year" class="form-select">
                    @for($y = now()->year; $y >= now()->year - 2; $y--)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-success"><i class="bi bi-download"></i> Generate PDF</button>
            </div>
        </form>
    </div>
</div>

{{-- Stats Summary --}}
@if(is_array($summary) && isset($summary['total_services']))
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-wrench"></i></div>
            <div class="stat-indicator" style="background:#3b82f6;"></div>
            <div class="stat-label">Total Servis</div>
            <div class="stat-value">{{ $summary['total_services'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-check-circle"></i></div>
            <div class="stat-indicator" style="background:#10b981;"></div>
            <div class="stat-label">Selesai</div>
            <div class="stat-value">{{ $summary['completed_services'] }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-indicator" style="background:#f59e0b;"></div>
            <div class="stat-label">Revenue</div>
            <div class="stat-value">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-clock"></i></div>
            <div class="stat-indicator" style="background:#7c3aed;"></div>
            <div class="stat-label">Rata-rata Waktu</div>
            <div class="stat-value">{{ $summary['avg_hours'] }} jam</div>
        </div>
    </div>
</div>
@elseif(is_array($summary) && isset($summary['months']))
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-wrench"></i></div>
            <div class="stat-indicator" style="background:#3b82f6;"></div>
            <div class="stat-label">Total Servis (Tahunan)</div>
            <div class="stat-value">{{ $summary['total']['total_services'] }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-indicator" style="background:#10b981;"></div>
            <div class="stat-label">Total Revenue</div>
            <div class="stat-value">Rp {{ number_format($summary['total']['total_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-clock"></i></div>
            <div class="stat-indicator" style="background:#f59e0b;"></div>
            <div class="stat-label">Rata-rata Waktu</div>
            <div class="stat-value">{{ $summary['total']['avg_hours'] }} jam</div>
        </div>
    </div>
</div>
@endif

{{-- Yearly Chart --}}
@if(is_array($summary) && isset($summary['months']))
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-bar-chart me-2"></i>Grafik Tahunan</div>
    <div class="card-body">
        <canvas id="yearChart" height="200"></canvas>
    </div>
</div>
@endif

{{-- Table: Service List & Summary Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-range me-2"></i>Laporan Per Periode</div>
            <div class="card-body">
                <form action="{{ route('reports.index') }}" method="GET" class="row g-3">
                    <input type="hidden" name="period" value="monthly">
                    <div class="col-md-5">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Ringkasan Bulan Ini</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Servis</div>
                            <div class="stat-value">{{ $summary['total_services'] ?? $summary['total']['total_services'] ?? $services->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Pending</div>
                            <div class="stat-value text-warning">{{ $services->where('status', 'pending')->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">In Progress</div>
                            <div class="stat-value text-primary">{{ $services->where('status', 'progress')->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Selesai</div>
                            <div class="stat-value text-success">{{ $services->where('status', 'done')->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Dibatalkan</div>
                            <div class="stat-value text-danger">{{ $services->where('status', 'cancelled')->count() }}</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card">
                            <div class="stat-label">Total Pendapatan</div>
                            <div class="stat-value text-success">Rp {{ number_format($services->where('status', 'done')->sum('total_cost'), 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-list me-2"></i>Daftar Servis</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Pelanggan</th>
                                <th>Plat</th>
                                <th>Tipe</th>
                                <th>Status</th>
                                <th>Biaya</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($services as $svc)
                            <tr>
                                <td>{{ $svc->customer_name }}</td>
                                <td><strong>{{ $svc->vehicle_plate }}</strong></td>
                                <td>{{ ucfirst(str_replace('_', ' ', $svc->type)) }}</td>
                                <td>
                                    @php
                                        $sc = ['pending' => 'warning', 'progress' => 'primary', 'done' => 'success', 'cancelled' => 'danger'];
                                    @endphp
                                    <span class="badge bg-{{ $sc[$svc->status] ?? 'secondary' }}">{{ $svc->status }}</span>
                                </td>
                                <td>Rp {{ number_format($svc->total_cost, 0, ',', '.') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-3" style="color:#94a3b8;">Tidak ada data servis di periode ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Archives --}}
<div class="card">
    <div class="card-header"><i class="bi bi-archive me-2"></i>Arsip Laporan</div>
    <div class="card-body">
        @if($archives->count() > 0)
        <div class="archive-list">
            @foreach($archives as $a)
            <div class="archive-item">
                <div class="info">
                    <div class="title">{{ $a->title }}</div>
                    <div class="meta">{{ $a->created_at->format('d/m/Y H:i') }} • {{ $a->type }}</div>
                </div>
                <div>
                    <span class="badge badge-purple">PDF</span>
                    <a href="{{ asset('storage/' . $a->file_path) }}" target="_blank" class="btn btn-primary btn-sm ms-2">
                        <i class="bi bi-download"></i> Download
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-center py-4" style="color:#94a3b8;">Belum ada laporan yang diarsipkan</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if(is_array($summary) && isset($summary['months']))
<script>
const months = @json($summary['months']);
const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

new Chart(document.getElementById('yearChart'), {
    type: 'bar',
    data: {
        labels: monthNames,
        datasets: [
            {
                label: 'Total Servis',
                data: months.map(m => m.total_services),
                backgroundColor: 'rgba(37,99,235,0.7)',
                borderRadius: 4,
            },
            {
                label: 'Revenue (Rp 000)',
                data: months.map(m => m.total_revenue / 1000),
                backgroundColor: 'rgba(5,150,105,0.7)',
                borderRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: { usePointStyle: true, padding: 20 }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endif
@endpush