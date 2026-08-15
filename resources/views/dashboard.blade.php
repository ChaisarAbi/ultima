@extends('layouts.app')

@section('title', 'Dashboard Manajer')

@section('content')
{{-- Stat Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:var(--accent);"></div>
            <i class="bi bi-tools stat-icon-bg"></i>
            <div class="stat-label">Total Servis</div>
            <div class="stat-value">{{ $totalServices }}</div>
            <div class="stat-sub">Semua waktu</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#f59e0b;"></div>
            <i class="bi bi-arrow-repeat stat-icon-bg"></i>
            <div class="stat-label">Servis Aktif</div>
            <div class="stat-value">{{ $activeServices }}</div>
            <div class="stat-sub">Pending / Progress</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#10b981;"></div>
            <i class="bi bi-check-circle stat-icon-bg"></i>
            <div class="stat-label">Selesai Hari Ini</div>
            <div class="stat-value">{{ $completedToday }}</div>
            <div class="stat-sub">Bulan ini: {{ $completedMonth }}</div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#7c3aed;"></div>
            <i class="bi bi-cash-stack stat-icon-bg"></i>
            <div class="stat-label">Revenue Bulan Ini</div>
            <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
            <div class="stat-sub">
                @if($revenueGrowth > 0)
                    <span style="color:#10b981;">↑ {{ $revenueGrowth }}%</span>
                @elseif($revenueGrowth < 0)
                    <span style="color:#ef4444;">↓ {{ abs($revenueGrowth) }}%</span>
                @else
                    <span style="color:#94a3b8;">—</span>
                @endif
                dari bulan lalu
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#0891b2;"></div>
            <i class="bi bi-clock-history stat-icon-bg"></i>
            <div class="stat-label">Rata-rata Pengerjaan</div>
            <div class="stat-value">{{ number_format($avgCompletionHours, 1) }} <small style="font-size:.75rem;font-weight:500;color:#64748b;">jam</small></div>
            <div class="stat-sub">Bulan ini</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#dc2626;"></div>
            <i class="bi bi-exclamation-triangle stat-icon-bg"></i>
            <div class="stat-label">Stok Menipis</div>
            <div class="stat-value" style="color:#dc2626;">{{ $lowStockParts }}</div>
            <div class="stat-sub">Spare part perlu restock</div>
        </div>
    </div>
    <div class="col-md-4 col-sm-6">
        <div class="card stat-card">
            <div class="stat-indicator" style="background:#059669;"></div>
            <i class="bi bi-graph-up-arrow stat-icon-bg"></i>
            <div class="stat-label">Revenue 7 Hari</div>
            <div class="stat-value">Rp {{ number_format($weeklyRevenue, 0, ',', '.') }}</div>
            <div class="stat-sub">Pekan terakhir</div>
        </div>
    </div>
</div>

{{-- Charts Row --}}
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <h3>📈 Servis Masuk (14 Hari) vs Prediksi LSTM</h3>
            <div class="chart-container chart-container-md">
                <canvas id="comparisonChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="chart-card">
            <h3>🔮 Prediksi Harian</h3>
            <div id="predictionList">
                <p style="text-align:center;color:#94a3b8;padding:40px;">Memuat...</p>
            </div>
        </div>
    </div>
</div>

{{-- Bottom Row --}}
<div class="row g-3">
    {{-- Status Pie --}}
    <div class="col-lg-4">
        <div class="chart-card">
            <h3>📊 Status Servis</h3>
            <div class="status-list">
                <div class="status-item"><span class="status-dot dot-pending"></span> Pending: <strong>{{ $servicesByStatus['pending'] }}</strong></div>
                <div class="status-item"><span class="status-dot dot-progress"></span> Progress: <strong>{{ $servicesByStatus['in_progress'] }}</strong></div>
                <div class="status-item"><span class="status-dot dot-done"></span> Selesai: <strong>{{ $servicesByStatus['completed'] }}</strong></div>
                <div class="status-item"><span class="status-dot dot-cancelled"></span> Cancelled: <strong>{{ $servicesByStatus['cancelled'] }}</strong></div>
            </div>
            <div class="chart-container chart-container-sm" style="max-width:260px;margin:0 auto;">
                <canvas id="statusPieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Spare Parts --}}
    <div class="col-lg-4">
        <div class="chart-card">
            <h3>🔧 Spare Part Paling Laris</h3>
            <div class="table-wrapper">
                <table>
                    <thead><tr><th>Spare Part</th><th>Total Terpakai</th></tr></thead>
                    <tbody>
                        @forelse($topSpareParts as $sp)
                        <tr><td>{{ $sp->name }}</td><td><strong>{{ $sp->total_used }}</strong> pcs</td></tr>
                        @empty
                        <tr><td colspan="2" style="text-align:center;color:#94a3b8;">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent Activities --}}
    <div class="col-lg-4">
        <div class="chart-card">
            <h3>🕐 Aktivitas Terbaru</h3>
            <div>
                @forelse($recentActivities as $log)
                <div class="activity-item">
                    <div><strong>{{ $log->user->name ?? 'System' }}</strong> {{ $log->action }}</div>
                    <div class="time">{{ $log->created_at->diffForHumans() }}</div>
                </div>
                @empty
                <p style="text-align:center;color:#94a3b8;padding:20px;">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// === SAFE DATE PARSER ===
function safeParse(str) {
    if (!str) return new Date();
    const clean = str.split('T')[0].split(' ')[0];
    const p = clean.split('-');
    return new Date(+p[0], +p[1]-1, +p[2]);
}
function fmtDateID(str) {
    const d = safeParse(str);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
}
function fmtDateFull(str) {
    const d = safeParse(str);
    return d.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short', year: 'numeric' });
}

// ==========================
// 1. Histori vs Prediksi LSTM
// ==========================
const logs = @json($recentLogs);
const futurePreds = @json($futurePredictions);

const histLabels = logs.map(l => fmtDateID(l.log_date));
const histValues = logs.map(l => l.total_services);
const predLabels = futurePreds.map(p => fmtDateID(p.target_date));
const predValues = futurePreds.map(p => p.predicted_value);

const combinedLabels = [...histLabels, ...predLabels];
const histExtended = [...histValues, ...Array(predLabels.length).fill(null)];
const predExtended = [...Array(histLabels.length).fill(null), ...predValues];

new Chart(document.getElementById('comparisonChart'), {
    type: 'line',
    data: {
        labels: combinedLabels,
        datasets: [
            {
                label: 'Histori Servis',
                data: histExtended,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                fill: true,
                tension: 0.3,
                pointRadius: 3,
                pointBackgroundColor: '#2563eb',
                spanGaps: false,
            },
            {
                label: 'Prediksi LSTM',
                data: predExtended,
                borderColor: '#7c3aed',
                backgroundColor: 'rgba(124,58,237,0.1)',
                fill: true,
                tension: 0.3,
                pointRadius: 5,
                pointBackgroundColor: '#7c3aed',
                borderDash: [6, 3],
                borderWidth: 2,
                spanGaps: true,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, padding: 20, font: { size: 12 } } },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        if (ctx.parsed.y === null) return null;
                        return ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(0) + ' servis';
                    }
                }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0 } },
            x: { grid: { display: false }, ticks: { maxTicksLimit: 25, font: { size: 10 } } }
        },
        interaction: { intersect: false, mode: 'index' }
    }
});

// ==========================
// 2. Prediction List
// ==========================
const preds = @json($predictions);
const predContainer = document.getElementById('predictionList');
if (preds.length > 0) {
    let html = '<div style="display:flex;flex-direction:column;gap:8px;">';
    preds.forEach(p => {
        const dateStr = fmtDateFull(p.target_date);
        const val = Math.round(p.predicted_value);
        const actual = p.actual_value !== null ? Math.round(p.actual_value) : null;
        let accuracy = null;
        if (actual !== null && actual > 0) {
            accuracy = Math.round(100 - Math.abs(actual - val) / actual * 100);
        }
        html += '<div class="pred-item">';
        html += '<div style="display:flex;align-items:center;gap:10px;">';
        html += '<span style="width:8px;height:8px;border-radius:50%;background:' + (actual !== null ? '#10b981' : '#7c3aed') + ';"></span>';
        html += '<span style="font-size:13px;font-weight:500;color:#334155;">' + dateStr + '</span>';
        html += '</div>';
        html += '<div style="display:flex;align-items:center;gap:12px;margin-top:4px;">';
        html += '<span style="font-size:15px;font-weight:700;color:#7c3aed;">' + val + ' servis</span>';
        if (actual !== null) {
            html += '<span style="font-size:11px;font-weight:600;color:#065f46;background:#d1fae5;padding:2px 10px;border-radius:12px;">Aktual: ' + actual + '</span>';
            if (accuracy !== null) {
                const accColor = accuracy >= 80 ? '#059669' : (accuracy >= 60 ? '#d97706' : '#dc2626');
                html += '<span style="font-size:11px;font-weight:600;color:' + accColor + ';">' + accuracy + '% akurat</span>';
            }
        } else {
            html += '<span style="font-size:11px;color:#94a3b8;">(prediksi)</span>';
        }
        html += '</div></div>';
    });
    html += '<div style="margin-top:12px;text-align:right;">';
    html += '<a href="{{ route('prediction') }}" style="font-size:12px;color:#2563eb;text-decoration:none;font-weight:500;">Lihat detail prediksi →</a>';
    html += '</div></div>';
    predContainer.innerHTML = html;
} else {
    predContainer.innerHTML = '<p style="text-align:center;color:#94a3b8;padding:40px;">Belum ada data prediksi. <a href="{{ route('prediction') }}" style="color:#2563eb;text-decoration:underline;">Generate prediksi</a> terlebih dahulu.</p>';
}

// ==========================
// 3. Status Pie Chart
// ==========================
const statusData = @json($servicesByStatus);
new Chart(document.getElementById('statusPieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending', 'Progress', 'Selesai', 'Cancelled'],
        datasets: [{
            data: [statusData.pending, statusData.in_progress, statusData.completed, statusData.cancelled],
            backgroundColor: ['#f59e0b', '#3b82f6', '#10b981', '#ef4444'],
            borderWidth: 0,
        }]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { position: 'right', labels: { usePointStyle: true, padding: 15, font: { size: 12 } } } },
        cutout: '65%',
    }
});
</script>
@endpush