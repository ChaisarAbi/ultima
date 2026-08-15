@extends('layouts.app')

@section('title', 'Prediksi LSTM')

@push('styles')
<style>
.predict-card { background: #fff; border-radius: var(--radius); box-shadow: var(--card-shadow); padding: 1.25rem; transition: box-shadow .2s; margin-bottom: 1.25rem; }
.predict-card:hover { box-shadow: var(--card-shadow-hover); }

.charts-row { display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem; }

.row-prediction td { background: #faf5ff !important; }
.row-prediction:hover td { background: #f3e8ff !important; }

.badge-blue { background: #dbeafe; color: #1e40af; }
.badge-green { background: #d1fae5; color: #065f46; }
.badge-purple { background: #ede9fe; color: #5b21b6; }
.badge-yellow { background: #fef3c7; color: #92400e; }

.spinner {
    width: 24px; height: 24px;
    border: 3px solid #bfdbfe;
    border-top: 3px solid var(--accent);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* ======= LSTM ANIMASI PREDICTION ======= */
@keyframes progressPulse {
    0% { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}
@keyframes brainWave {
    0%, 100% { opacity: 0.3; transform: scaleY(0.5); }
    50% { opacity: 1; transform: scaleY(1); }
}
@keyframes nodePulse {
    0%, 100% { box-shadow: 0 0 4px rgba(124,58,237,0.3); }
    50% { box-shadow: 0 0 20px rgba(124,58,237,0.8); }
}
@keyframes particleFade {
    0% { opacity: 1; transform: translateY(0) scale(1); }
    100% { opacity: 0; transform: translateY(-30px) scale(0); }
}
@keyframes scanline {
    0% { top: -10%; }
    100% { top: 110%; }
}
@keyframes shimmerSlide {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* Animated LSTM loader container */
.lstm-loader {
    display: none;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #3730a3 100%);
    border-radius: 12px;
    padding: 24px;
    margin-top: 16px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 8px 32px rgba(124,58,237,0.3);
    border: 1px solid rgba(124,58,237,0.2);
}
.lstm-loader.active { display: block; }

/* Scanline effect */
.lstm-loader::before {
    content: '';
    position: absolute;
    left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, transparent, rgba(124,58,237,0.6), transparent);
    animation: scanline 2s ease-in-out infinite;
    z-index: 1;
}

/* Shimmer overlay */
.lstm-loader::after {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.03), transparent);
    animation: shimmerSlide 3s ease-in-out infinite;
    pointer-events: none;
}

/* Neural network visualization */
.lstm-network {
    display: flex;
    justify-content: space-around;
    align-items: center;
    height: 60px;
    margin: 16px 0;
    position: relative;
    z-index: 2;
}

.lstm-layer {
    display: flex;
    flex-direction: column;
    gap: 6px;
    align-items: center;
}

.lstm-node {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(124,58,237,0.6);
    border: 1px solid rgba(167,139,250,0.4);
    animation: nodePulse 2s ease-in-out infinite;
}
.lstm-node:nth-child(even) { animation-delay: 0.3s; }
.lstm-node:nth-child(3n) { animation-delay: 0.6s; }
.lstm-node:nth-child(5n) { animation-delay: 0.9s; }

.lstm-connection {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, rgba(124,58,237,0.2), rgba(167,139,250,0.6), rgba(124,58,237,0.2));
    position: relative;
    overflow: hidden;
    margin: 0 8px;
}
.lstm-connection::after {
    content: '';
    position: absolute;
    width: 30px;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(167,139,250,0.8), transparent);
    animation: progressPulse 2s linear infinite;
}

/* Progress bar animated */
.lstm-progress-container {
    position: relative;
    z-index: 2;
    margin-top: 8px;
}
.lstm-progress-bar {
    height: 6px;
    background: rgba(255,255,255,0.1);
    border-radius: 3px;
    overflow: hidden;
    position: relative;
}
.lstm-progress-fill {
    height: 100%;
    width: 0%;
    border-radius: 3px;
    background: linear-gradient(90deg, #7c3aed, #a78bfa, #7c3aed);
    background-size: 200% 100%;
    animation: progressPulse 1.5s linear infinite;
    transition: width 0.5s ease;
}

/* Status text */
.lstm-status {
    display: flex;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 12px;
    color: rgba(255,255,255,0.7);
    position: relative;
    z-index: 2;
}
.lstm-status-text {
    display: flex;
    align-items: center;
    gap: 6px;
}
.lstm-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #a78bfa;
    animation: nodePulse 1s ease-in-out infinite;
}

/* Particles */
.lstm-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0; left: 0;
    pointer-events: none;
    z-index: 1;
}
.lstm-particle {
    position: absolute;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    background: rgba(167,139,250,0.6);
    animation: particleFade 3s ease-out infinite;
}
.lstm-particle:nth-child(1) { left: 10%; animation-delay: 0s; }
.lstm-particle:nth-child(2) { left: 30%; animation-delay: 0.5s; }
.lstm-particle:nth-child(3) { left: 50%; animation-delay: 1s; }
.lstm-particle:nth-child(4) { left: 70%; animation-delay: 1.5s; }
.lstm-particle:nth-child(5) { left: 90%; animation-delay: 2s; }

/* Button loading state */
.btn-loading {
    position: relative;
    overflow: hidden;
    pointer-events: none;
}
.btn-loading::after {
    content: '';
    position: absolute;
    top: 0; left: -100%;
    width: 200%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.15), transparent);
    animation: shimmerSlide 1.5s ease-in-out infinite;
}

.chart-container-pred { position: relative; width: 100%; height: 380px; }

.table-scroll { overflow-x: auto; max-height: 420px; overflow-y: auto; }
.table-scroll th { position: sticky; top: 0; z-index: 1; }

@media (max-width: 768px) {
    .charts-row { grid-template-columns: 1fr; }
    .chart-container-pred { height: 300px; }
}
</style>
@endpush

@section('content')
<div class="page-actions">
    <h5><i class="bi bi-magic me-2"></i>Prediksi LSTM</h5>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-crosshair"></i></div>
            <div class="stat-indicator" style="background:#3b82f6;"></div>
            <div class="stat-label">Akurasi Rata-rata</div>
            <div style="display:flex;align-items:center;gap:10px;margin-top:6px;">
                <div style="flex:1;height:8px;background:#e5e7eb;border-radius:4px;overflow:hidden;">
                    <div style="height:100%;width:{{ $avgAccuracy }}%;border-radius:4px;background:{{ $avgAccuracy > 80 ? '#059669' : ($avgAccuracy > 60 ? '#d97706' : '#dc2626') }};transition:width 1s ease;"></div>
                </div>
                <span style="font-size:18px;font-weight:700;">{{ $avgAccuracy }}%</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-calculator"></i></div>
            <div class="stat-indicator" style="background:#10b981;"></div>
            <div class="stat-label">Total Prediksi</div>
            <div class="stat-value">{{ $totalPredictions }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-check-circle"></i></div>
            <div class="stat-indicator" style="background:#f59e0b;"></div>
            <div class="stat-label">Dengan Data Aktual</div>
            <div class="stat-value">{{ $withActual }}</div>
            <div class="stat-sub">Akurasi terverifikasi</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card stat-card">
            <div class="stat-icon-bg"><i class="bi bi-clock"></i></div>
            <div class="stat-indicator" style="background:#7c3aed;"></div>
            <div class="stat-label">Prediksi Mendatang</div>
            <div class="stat-value">{{ $futureCount }}</div>
            <div class="stat-sub">{{ $futurePredictions->count() }} hari ke depan</div>
        </div>
    </div>
</div>

<!-- Generate Form -->
<div class="card mb-4">
    <div class="card-header"><i class="bi bi-gear me-2"></i>Generate Prediksi LSTM</div>
    <div class="card-body">
        <form id="predictionForm" action="{{ route('prediction.generate') }}" method="POST" class="d-flex flex-wrap gap-3 align-items-end">
            @csrf
            <div>
                <label class="form-label">Metrik</label>
                <select name="metric" class="form-select">
                    <option value="total_services">Total Servis</option>
                    <option value="total_revenue">Revenue (Rp)</option>
                </select>
            </div>
            <div>
                <label class="form-label">Jangka Waktu</label>
                <select name="steps" class="form-select">
                    <option value="7">7 Hari</option>
                    <option value="14">14 Hari</option>
                    <option value="30">30 Hari</option>
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary" id="generateBtn">
                    <i class="bi bi-rocket-takeoff"></i> Generate Prediksi
                </button>
            </div>
        </form>

        <!-- LSTM Animated Loader -->
        <div class="lstm-loader" id="lstmLoader">
            <div class="lstm-particles">
                <div class="lstm-particle"></div>
                <div class="lstm-particle"></div>
                <div class="lstm-particle"></div>
                <div class="lstm-particle"></div>
                <div class="lstm-particle"></div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;position:relative;z-index:2;">
                <div style="width:36px;height:36px;border-radius:8px;background:rgba(124,58,237,0.2);display:flex;align-items:center;justify-content:center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#a78bfa" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <div>
                    <div style="font-weight:600;font-size:14px;color:#fff;">LSTM Neural Network</div>
                    <div style="font-size:11px;color:rgba(255,255,255,0.5);">Memproses prediksi servis...</div>
                </div>
            </div>

            <!-- Neural Network Visualization -->
            <div class="lstm-network">
                <div class="lstm-layer">
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                </div>
                <div class="lstm-connection"></div>
                <div class="lstm-layer">
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                </div>
                <div class="lstm-connection"></div>
                <div class="lstm-layer">
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                </div>
                <div class="lstm-connection"></div>
                <div class="lstm-layer">
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                </div>
                <div class="lstm-connection"></div>
                <div class="lstm-layer">
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                    <div class="lstm-node"></div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="lstm-progress-container">
                <div class="lstm-progress-bar">
                    <div class="lstm-progress-fill" id="progressFill" style="width:10%;"></div>
                </div>
                <div class="lstm-status">
                    <span class="lstm-status-text">
                        <span class="lstm-status-dot"></span>
                        <span id="lstmStatusText">Initializing LSTM model...</span>
                    </span>
                    <span id="lstmProgressText">10%</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="charts-row">
    <div class="card">
        <div class="card-header"><i class="bi bi-bar-chart-line me-2"></i>Histori Servis vs Prediksi LSTM</div>
        <div class="card-body">
            <div class="chart-container-pred">
                <canvas id="mainChart"></canvas>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header"><i class="bi bi-table me-2"></i>Detail Prediksi</div>
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Prediksi</th>
                            <th>Aktual</th>
                            <th>Selisih</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $shownLogs = $recentLogs->slice(-14); @endphp
                        @forelse($shownLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->log_date)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-blue">Aktual</span></td>
                            <td>-</td>
                            <td><strong>{{ number_format($log->total_services, 0) }}</strong></td>
                            <td><span style="color:#9ca3af;">-</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:30px;">Belum ada data log</td></tr>
                        @endforelse

                        @forelse($futurePredictions as $p)
                        <tr class="row-prediction">
                            <td>{{ \Carbon\Carbon::parse($p->target_date)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-purple">Prediksi</span></td>
                            <td><strong>{{ number_format($p->predicted_value, 0) }}</strong></td>
                            <td><span style="color:#9ca3af;">-</span></td>
                            <td><span style="color:#9ca3af;">-</span></td>
                        </tr>
                        @empty
                        @if($shownLogs->count() === 0)
                        <tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:30px;">Belum ada data. Generate prediksi terlebih dahulu.</td></tr>
                        @endif
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="padding:.75rem 1rem;font-size:12px;color:#9ca3af;display:flex;gap:16px;border-top:1px solid rgba(0,0,0,0.04);">
                <span><span class="badge badge-blue" style="font-size:11px;">Aktual</span> Data historis servis</span>
                <span><span class="badge badge-purple" style="font-size:11px;">Prediksi</span> Hasil LSTM</span>
            </div>
        </div>
    </div>
</div>

<!-- ===== ADDITIONAL CHARTS ROW 1 ===== -->
<div class="row g-3 mb-4">
    <!-- Line Chart: Tren 90 Hari -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-graph-up me-2"></i>Tren Servis 90 Hari (Aktual + Prediksi)</div>
            <div class="card-body">
                <div class="chart-container-pred">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Doughnut: Distribusi Akurasi -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Distribusi Akurasi Prediksi</div>
            <div class="card-body">
                <div class="chart-container-pred" style="height:300px;">
                    <canvas id="accuracyChart"></canvas>
                </div>
                <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px;font-size:12px;">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#059669;"></span> >90% ({{ $accuracyDist['sangat_akurat'] }})</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3b82f6;"></span> 70-90% ({{ $accuracyDist['akurat'] }})</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#d97706;"></span> 50-70% ({{ $accuracyDist['cukup'] }})</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#dc2626;"></span> <50% ({{ $accuracyDist['kurang'] }})</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== ADDITIONAL CHARTS ROW 2 ===== -->
<div class="row g-3 mb-4">
    <!-- Revenue Prediction -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="bi bi-cash-stack me-2"></i>Prediksi Revenue (14 Hari)</div>
            <div class="card-body">
                <div class="chart-container-pred">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Weekly Pattern -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-calendar-week me-2"></i>Rata-rata Servis per Hari</div>
            <div class="card-body">
                <div class="chart-container-pred" style="height:280px;">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Completed vs Cancelled -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-header"><i class="bi bi-check2-circle me-2"></i>Servis Selesai vs Batal</div>
            <div class="card-body">
                <div class="chart-container-pred" style="height:200px;">
                    <canvas id="completionChart"></canvas>
                </div>
                <div style="display:flex;justify-content:center;gap:20px;margin-top:8px;font-size:13px;">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#059669;"></span> Selesai {{ number_format($completedCount, 0) }}</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#ef4444;"></span> Batal {{ number_format($cancelledCount, 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Past Predictions -->
@if($pastPredictions->count() > 0)
<div class="card">
    <div class="card-header"><i class="bi bi-bar-chart-steps me-2"></i>Perbandingan Prediksi vs Aktual (Historis)</div>
    <div class="card-body p-0">
        <div class="table-scroll" style="max-height:300px;">
            <table class="table table-hover mb-0">
                <thead>
                    <tr><th>Tanggal</th><th>Prediksi</th><th>Aktual</th><th>Selisih</th><th>Akurasi</th></tr>
                </thead>
                <tbody>
                    @foreach($pastPredictions as $p)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($p->target_date)->format('d/m/Y') }}</td>
                        <td>{{ number_format($p->predicted_value, 1) }}</td>
                        <td>{{ $p->actual_value !== null ? number_format($p->actual_value, 1) : '-' }}</td>
                        <td>
                            @if($p->actual_value !== null)
                                @php $diff = abs($p->predicted_value - $p->actual_value); @endphp
                                <span style="color: {{ $diff < 2 ? '#059669' : ($diff < 5 ? '#d97706' : '#dc2626') }};">
                                    ±{{ number_format($diff, 1) }}
                                </span>
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>
                        <td>
                            @if($p->actual_value !== null && $p->actual_value > 0)
                                @php $acc = max(0, min(100, (1 - abs($p->predicted_value - $p->actual_value) / max($p->predicted_value, $p->actual_value)) * 100)); @endphp
                                <span style="color: {{ $acc > 80 ? '#059669' : ($acc > 60 ? '#d97706' : '#dc2626') }};font-weight:600;">{{ number_format($acc, 0) }}%</span>
                            @else
                                <span style="color:#9ca3af;">-</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
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
function fmtDate(str) {
    const d = safeParse(str);
    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
}

// === Data from backend ===
const logs = @json($recentLogs);
const futurePreds = @json($futurePredictions);

const histLabels = logs.map(l => fmtDate(l.log_date));
const histValues = logs.map(l => l.total_services);
const predLabels = futurePreds.map(p => fmtDate(p.target_date));
const predValues = futurePreds.map(p => p.predicted_value);
const combinedLabels = [...histLabels, ...predLabels];
const histExtended = [...histValues, ...Array(predLabels.length).fill(null)];
const predExtended = [...Array(histLabels.length).fill(null), ...predValues];

const ctx = document.getElementById('mainChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: combinedLabels,
        datasets: [
            {
                label: 'Histori Servis',
                data: histExtended,
                backgroundColor: 'rgba(37,99,235,0.7)',
                borderColor: '#2563eb',
                borderWidth: 1,
                borderRadius: 3,
                barPercentage: 0.6,
                categoryPercentage: 0.8,
            },
            {
                label: 'Prediksi LSTM',
                data: predExtended,
                backgroundColor: 'rgba(124,58,237,0.7)',
                borderColor: '#7c3aed',
                borderWidth: 1,
                borderRadius: 3,
                barPercentage: 0.6,
                categoryPercentage: 0.8,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
                labels: { usePointStyle: true, padding: 20, font: { size: 12 } }
            },
            tooltip: {
                callbacks: {
                    label: function(ctx) {
                        if (ctx.parsed.y === null || ctx.parsed.y === undefined) return null;
                        return ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(0) + ' servis';
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: { precision: 0 }
            },
            x: {
                grid: { display: false },
                ticks: { maxTicksLimit: 20, font: { size: 10 } }
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        }
    }
});

// === LSTM Animated Loader ===
const lstmLoader = document.getElementById('lstmLoader');
const progressFill = document.getElementById('progressFill');
const lstmStatusText = document.getElementById('lstmStatusText');
const lstmProgressText = document.getElementById('lstmProgressText');

const lstmPhases = [
    { pct: 15, text: 'Loading historical data...' },
    { pct: 30, text: 'Normalizing time series...' },
    { pct: 45, text: 'Building LSTM layers...' },
    { pct: 55, text: 'Creating sequence batches...' },
    { pct: 70, text: 'Training LSTM model...' },
    { pct: 82, text: 'Generating forecast...' },
    { pct: 92, text: 'Calculating confidence intervals...' },
    { pct: 100, text: 'Finalizing predictions...' },
];

let phaseIndex = 0;

function animateLSTM() {
    if (phaseIndex >= lstmPhases.length) {
        lstmStatusText.textContent = '✅ Prediction complete! Refreshing...';
        lstmProgressText.textContent = '100%';
        progressFill.style.width = '100%';
        return;
    }
    const phase = lstmPhases[phaseIndex];
    progressFill.style.width = phase.pct + '%';
    lstmStatusText.textContent = phase.text;
    lstmProgressText.textContent = phase.pct + '%';
    phaseIndex++;

    // Random interval between 600-1200ms for natural feel
    const delay = 600 + Math.random() * 600;
    setTimeout(animateLSTM, delay);
}

// Submit form
document.getElementById('predictionForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('generateBtn');
    btn.disabled = true;
    btn.classList.add('btn-loading');
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

    // Show animated LSTM loader
    lstmLoader.classList.add('active');
    phaseIndex = 0;
    progressFill.style.width = '10%';
    lstmStatusText.textContent = 'Initializing LSTM model...';
    lstmProgressText.textContent = '10%';
    animateLSTM();
});

// ====== NEW CHARTS (with error handling) ======
console.log('=== Starting to render extra charts ===');
console.log('allLogs count:', @json($allLogs->count()));
console.log('revenuePredictions:', @json($revenuePredictions->count()));
console.log('revenueHistory:', @json($revenueHistory->count()));
console.log('accuracyDist:', @json($accuracyDist));
console.log('weeklyPattern:', @json($weeklyPattern));
console.log('completedCount:', {{ $completedCount }}, 'cancelledCount:', {{ $cancelledCount }});

// Helper to safely create chart
function safeChart(id, config) {
    try {
        const el = document.getElementById(id);
        if (!el) { console.warn('Element #' + id + ' not found'); return null; }
        const chart = new Chart(el, config);
        console.log('Chart ' + id + ' created successfully');
        return chart;
    } catch(e) {
        console.error('Failed to create chart ' + id + ':', e);
        return null;
    }
}

// --- 1. Trend Chart (30 days line) ---
const allLogs = @json($allLogs);
const trendLabels = allLogs.map(l => fmtDate(l.log_date));
const trendValues = allLogs.map(l => l.total_services);

safeChart('trendChart', {
    type: 'line',
    data: {
        labels: trendLabels,
        datasets: [{
            label: 'Servis Harian',
            data: trendValues,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59,130,246,0.08)',
            fill: true,
            tension: 0.3,
            pointRadius: 2,
            pointHoverRadius: 5,
            borderWidth: 2,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.y !== null ? ctx.parsed.y.toFixed(0) + ' servis' : ''
                }
            }
        },
        scales: {
            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.04)' }, ticks: { precision: 0 } },
            x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 9 } } }
        },
        interaction: { intersect: false, mode: 'index' }
    }
});

// --- 2. Accuracy Doughnut ---
const accDist = @json($accuracyDist);
safeChart('accuracyChart', {
    type: 'doughnut',
    data: {
        labels: ['>90% (Sangat Akurat)', '70-90% (Akurat)', '50-70% (Cukup)', '<50% (Kurang)'],
        datasets: [{
            data: [accDist.sangat_akurat, accDist.akurat, accDist.cukup, accDist.kurang],
            backgroundColor: ['#059669', '#3b82f6', '#d97706', '#dc2626'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label.split('(')[0].trim() + ': ' + ctx.parsed + ' prediksi'
                }
            }
        },
        cutout: '65%',
    }
});

// --- 3. Revenue Prediction (bar) ---
const revPreds = @json($revenuePredictions);
const revHist = @json($revenueHistory);
const revLabels = revHist.length > 0 && revPreds.length > 0
    ? [...revHist.map(l => fmtDate(l.log_date)), ...revPreds.map(p => fmtDate(p.target_date))]
    : [];
const revHistValues = revHist.map(l => (l.predicted_value || 0) / 1000000);
const revPredValues = revPreds.map(p => (p.predicted_value || 0) / 1000000);
// Pad with nulls
const fullRevHist = [...revHistValues, ...Array(revPreds.length).fill(null)];
const fullRevPred = [...Array(revHist.length).fill(null), ...revPredValues];

safeChart('revenueChart', {
    type: 'bar',
    data: {
        labels: revLabels,
        datasets: [
            {
                label: 'Revenue Aktual',
                data: fullRevHist,
                backgroundColor: 'rgba(16,185,129,0.7)',
                borderColor: '#10b981',
                borderWidth: 1,
                borderRadius: 3,
                barPercentage: 0.5,
            },
            {
                label: 'Prediksi Revenue',
                data: fullRevPred,
                backgroundColor: 'rgba(124,58,237,0.7)',
                borderColor: '#7c3aed',
                borderWidth: 1,
                borderRadius: 3,
                barPercentage: 0.5,
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'top', labels: { usePointStyle: true, font: { size: 11 } } },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.y !== null ? ctx.dataset.label + ': Rp' + (ctx.parsed.y * 1e6).toLocaleString('id-ID') : ''
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: { color: 'rgba(0,0,0,0.04)' },
                ticks: {
                    callback: v => 'Rp' + (v * 1e6).toLocaleString('id-ID')
                }
            },
            x: { grid: { display: false }, ticks: { maxTicksLimit: 10, font: { size: 9 } } }
        },
        interaction: { intersect: false, mode: 'index' }
    }
});

// --- 4. Weekly Pattern (radar) ---
const weeklyData = @json($weeklyPattern);
safeChart('weeklyChart', {
    type: 'radar',
    data: {
        labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', "Jum'at", 'Sabtu', 'Minggu'],
        datasets: [{
            label: 'Rata-rata Servis',
            data: [weeklyData['1']||0, weeklyData['2']||0, weeklyData['3']||0, weeklyData['4']||0, weeklyData['5']||0, weeklyData['6']||0, weeklyData['7']||0],
            backgroundColor: 'rgba(124,58,237,0.15)',
            borderColor: '#7c3aed',
            borderWidth: 2,
            pointBackgroundColor: '#7c3aed',
            pointRadius: 4,
            pointHoverRadius: 6,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.parsed.r !== null ? ctx.parsed.r.toFixed(1) + ' servis' : ''
                }
            }
        },
        scales: {
            r: {
                beginAtZero: true,
                ticks: { precision: 0, backdropColor: 'transparent', font: { size: 9 } },
                grid: { color: 'rgba(0,0,0,0.06)' },
                angleLines: { color: 'rgba(0,0,0,0.06)' },
                pointLabels: { font: { size: 10 } }
            }
        }
    }
});

// --- 5. Completed vs Cancelled Doughnut ---
safeChart('completionChart', {
    type: 'doughnut',
    data: {
        labels: ['Selesai', 'Batal'],
        datasets: [{
            data: [{{ $completedCount }}, {{ $cancelledCount }}],
            backgroundColor: ['#059669', '#ef4444'],
            borderWidth: 2,
            borderColor: '#fff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ctx.label + ': ' + ctx.parsed.toLocaleString('id-ID') + ' servis'
                }
            }
        },
        cutout: '60%',
    }
});

console.log('=== All extra charts done ===');
</script>
@endpush
