<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #333; line-height: 1.6; }
        .header { text-align: center; border-bottom: 3px solid #2563eb; padding-bottom: 15px; margin-bottom: 20px; }
        .logo-container { margin-bottom: 10px; }
        .header h1 { font-size: 22px; color: #2563eb; margin: 0 0 5px 0; }
        .header .subtitle { font-size: 13px; color: #6b7280; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 14px; font-weight: bold; color: #2563eb; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 10px; }
        
        .stats-grid { width: 100%; margin-bottom: 16px; }
        .stats-grid table { width: 100%; border-collapse: collapse; }
        .stats-grid td { width: 25%; padding: 8px; text-align: center; border: 1px solid #e5e7eb; }
        .stat-label { font-size: 10px; color: #6b7280; display: block; }
        .stat-value { font-size: 16px; font-weight: bold; color: #111827; display: block; margin-top: 2px; }

        table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        table.data th { background: #2563eb; color: white; padding: 8px 10px; text-align: left; font-size: 10px; text-transform: uppercase; }
        table.data td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; font-size: 10px; }
        table.data tr:nth-child(even) td { background: #f9fafb; }

        .badge { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 9px; font-weight: bold; }
        .badge-pending { background: #fef3c7; color: #92400e; }
        .badge-done { background: #d1fae5; color: #065f46; }
        .badge-progress { background: #dbeafe; color: #1e40af; }

        .footer { text-align: center; font-size: 9px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 20px; }

        .summary-box { border: 2px solid #2563eb; border-radius: 8px; padding: 15px; text-align: center; margin-bottom: 16px; }
        .summary-box .big-number { font-size: 28px; font-weight: bold; color: #2563eb; }

        .row { width: 100%; }
        .col-half { width: 48%; float: left; margin-right: 4%; }
        .col-half:last-child { margin-right: 0; }
        .clearfix::after { content: ""; clear: both; display: table; }

        .prediction-box { background: #f0f4ff; border-left: 4px solid #7c3aed; padding: 10px 14px; margin-bottom: 12px; }
        .prediction-box .pred-title { font-weight: bold; font-size: 12px; color: #5b21b6; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-container">
            {{-- Note: Logo removed from PDF because PHP dompdf doesn't support WebP format --}}
            {{-- PNG/JPG logos can be used if converted manually --}}
        </div>
        <h1>🔧 BMW ULTIMA</h1>
        <div class="subtitle">{{ $title }}</div>
        <div class="subtitle">Dicetak: {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    @if(isset($data['total_services']))
    <div class="summary-box">
        <div style="font-size:12px;font-weight:bold;color:#6b7280;margin-bottom:4px;">TOTAL REVENUE</div>
        <div class="big-number">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</div>
        <div style="font-size:11px;color:#6b7280;margin-top:4px;">{{ $data['completed_services'] }} dari {{ $data['total_services'] }} servis selesai</div>
    </div>

    <table class="stats-grid">
        <tr>
            <td><span class="stat-label">Total Servis</span><span class="stat-value">{{ $data['total_services'] }}</span></td>
            <td><span class="stat-label">Selesai</span><span class="stat-value">{{ $data['completed_services'] }}</span></td>
            <td><span class="stat-label">Rata-rata Waktu</span><span class="stat-value">{{ $data['avg_hours'] }} jam</span></td>
            <td><span class="stat-label">Spare Part Terpakai</span><span class="stat-value">{{ $data['total_spare_used'] ?? 0 }}</span></td>
        </tr>
        <tr>
            <td><span class="stat-label">Pending</span><span class="stat-value">{{ $data['pending'] }}</span></td>
            <td><span class="stat-label">Progress</span><span class="stat-value">{{ $data['in_progress'] }}</span></td>
            <td><span class="stat-label">Cancelled</span><span class="stat-value">{{ $data['cancelled'] }}</span></td>
            <td></td>
        </tr>
    </table>
    @endif

    @if(isset($data['months']))
    <div class="section">
        <div class="section-title">Ringkasan Tahunan</div>
        <table class="stats-grid">
            <tr>
                <td><span class="stat-label">Total Servis</span><span class="stat-value">{{ $data['total']['total_services'] }}</span></td>
                <td><span class="stat-label">Total Revenue</span><span class="stat-value">Rp {{ number_format($data['total']['total_revenue'], 0, ',', '.') }}</span></td>
                <td><span class="stat-label">Rata-rata Waktu</span><span class="stat-value">{{ $data['total']['avg_hours'] }} jam</span></td>
                <td><span class="stat-label">Spare Part</span><span class="stat-value">{{ $data['total']['total_spare_used'] ?? 0 }}</span></td>
            </tr>
        </table>

        <table class="data">
            <thead>
                <tr>
                    <th>Bulan</th>
                    <th>Total Servis</th>
                    <th>Selesai</th>
                    <th>Revenue</th>
                    <th>Rata-rata (jam)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data['months'] as $monthData)
                <tr>
                    <td><strong>{{ \Carbon\Carbon::create()->month($loop->index + 1)->format('F') }}</strong></td>
                    <td>{{ $monthData['total_services'] }}</td>
                    <td>{{ $monthData['completed_services'] }}</td>
                    <td>Rp {{ number_format($monthData['total_revenue'], 0, ',', '.') }}</td>
                    <td>{{ $monthData['avg_hours'] }} jam</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section clearfix">
        <div class="section-title">Top Spare Part Terpakai</div>
        <table class="data">
            <thead><tr><th>Nama Spare Part</th><th>Total Pakai</th><th>Harga Satuan</th><th>Subtotal</th></tr></thead>
            <tbody>
                @forelse($topSpareParts as $sp)
                <tr>
                    <td>{{ $sp->name }}</td>
                    <td>{{ $sp->total_used }} pcs</td>
                    <td>Rp {{ number_format($sp->price, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($sp->price * $sp->total_used, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;color:#9ca3af;">Belum ada data</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Prediksi LSTM</div>
        <div class="prediction-box">
            <div class="pred-title">🔮 Forecast Jumlah Servis</div>
            @forelse($predictions as $p)
                <span style="display:inline-block;margin-right:12px;margin-bottom:4px;">
                    {{ \Carbon\Carbon::parse($p->target_date)->format('d/m') }}: 
                    <strong>{{ number_format($p->predicted_value, 0) }}</strong>
                </span>
            @empty
                <span style="color:#9ca3af;">Belum ada data prediksi</span>
            @endforelse
        </div>
    </div>

    <div class="section">
        <div class="section-title">Servis Terbaru</div>
        <table class="data">
            <thead><tr><th>Pelanggan</th><th>Plat</th><th>Tipe</th><th>Status</th><th>Biaya</th></tr></thead>
            <tbody>
                @forelse($recentServices as $s)
                <tr>
                    <td>{{ $s->customer_name }}</td>
                    <td>{{ $s->vehicle_plate }}</td>
                    <td>{{ str_replace('_', ' ', ucfirst($s->type)) }}</td>
                    <td><span class="badge badge-{{ $s->status }}">{{ ucfirst($s->status) }}</span></td>
                    <td>Rp {{ number_format($s->total_cost, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;color:#9ca3af;">Belum ada data servis</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Laporan ini digenerate otomatis oleh Sistem BMW ULTIMA - {{ now()->format('Y') }}</p>
    </div>
</body>
</html>