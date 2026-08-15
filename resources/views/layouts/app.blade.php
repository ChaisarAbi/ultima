<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — BMW ULTIMA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --sidebar-width: 250px;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255,255,255,0.08);
            --sidebar-active: rgba(59,130,246,0.15);
            --sidebar-text: rgba(255,255,255,0.65);
            --sidebar-text-active: #fff;
            --accent: #2563eb;
            --accent-light: #3b82f6;
            --card-shadow: 0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --card-shadow-hover: 0 4px 12px rgba(0,0,0,0.08), 0 2px 4px rgba(0,0,0,0.04);
            --radius: 12px;
            --radius-sm: 8px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            font-size: .875rem;
            line-height: 1.6;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1040;
            overflow-y: auto;
            transition: transform .3s ease;
        }
        .sidebar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 1.25rem 1.25rem .75rem;
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: -.02em;
        }
        .sidebar .brand img {
            height: 36px;
            width: auto;
            object-fit: contain;
            border-radius: 4px;
        }
        .sidebar .brand i { font-size: 1.5rem; color: var(--accent-light); }
        .sidebar .divider {
            margin: .5rem 1rem;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar .nav-section {
            padding: .5rem 1.25rem .25rem;
            font-size: .65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,0.3);
        }
        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: .65rem 1.25rem;
            margin: 1px .5rem;
            color: var(--sidebar-text);
            text-decoration: none;
            border-radius: var(--radius-sm);
            font-size: .8125rem;
            font-weight: 500;
            transition: all .15s ease;
        }
        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .sidebar .nav-link:hover {
            color: var(--sidebar-text-active);
            background: var(--sidebar-hover);
        }
        .sidebar .nav-link.active {
            color: var(--sidebar-text-active);
            background: var(--sidebar-active);
        }
        .sidebar .nav-link.logout-btn {
            background: none;
            border: none;
            cursor: pointer;
            width: calc(100% - 1rem);
            font-size: .8125rem;
        }
        .sidebar .nav-link.logout-btn:hover {
            background: rgba(239,68,68,0.1);
            color: #fca5a5;
        }

        /* ===== MAIN ===== */
        .main-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0,0,0,0.04);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .topbar .page-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .topbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .topbar .user-info .role-badge {
            font-size: .7rem;
            font-weight: 600;
            padding: .25em .75em;
            border-radius: 100px;
            background: #e2e8f0;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .03em;
        }
        .topbar .user-info .avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #7c3aed);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .8rem;
        }
        .content-area {
            padding: 1.5rem;
        }

        /* ===== CARDS ===== */
        .card {
            border: none;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            background: #fff;
            transition: box-shadow .2s ease, transform .2s ease;
        }
        .card:hover { box-shadow: var(--card-shadow-hover); }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            font-weight: 600;
            padding: 1rem 1.25rem;
            font-size: .875rem;
            color: #334155;
        }
        .card-body { padding: 1.25rem; }
        .card-footer {
            background: transparent;
            border-top: 1px solid rgba(0,0,0,0.04);
            padding: .75rem 1.25rem;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 1.25rem;
        }
        .stat-card .stat-icon-bg {
            position: absolute;
            right: .75rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 2.5rem;
            opacity: .1;
            color: var(--accent);
        }
        .stat-card .stat-label {
            font-size: .75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #94a3b8;
            margin-bottom: .25rem;
        }
        .stat-card .stat-value {
            font-size: 1.75rem;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
        }
        .stat-card .stat-sub {
            font-size: .75rem;
            color: #64748b;
            margin-top: .15rem;
        }
        .stat-card .stat-indicator {
            width: 4px;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 4px 0 0 4px;
        }

        /* ===== TABLES ===== */
        .table th {
            font-weight: 600;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            color: #64748b;
            border-top: none;
            padding: .75rem .75rem .5rem;
            background: #f8fafc;
        }
        .table td {
            padding: .65rem .75rem;
            vertical-align: middle;
            color: #334155;
            border-color: rgba(0,0,0,0.04);
        }
        .table-hover tbody tr:hover {
            background: #f8fafc;
        }
        .table .empty-state {
            padding: 2.5rem;
            text-align: center;
            color: #94a3b8;
        }
        .table .empty-state i { font-size: 2rem; display: block; margin-bottom: .5rem; }

        /* ===== BADGES ===== */
        .badge {
            font-weight: 500;
            font-size: .75rem;
            padding: .3em .65em;
            border-radius: 100px;
        }

        /* ===== BUTTONS ===== */
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: .8125rem;
            padding: .4rem .9rem;
            transition: all .15s ease;
        }
        .btn-sm { padding: .25rem .6rem; font-size: .75rem; }
        .btn-primary { background: var(--accent); border-color: var(--accent); }
        .btn-primary:hover { background: #1d4ed8; border-color: #1d4ed8; transform: translateY(-1px); box-shadow: 0 2px 8px rgba(37,99,235,0.3); }
        .btn-outline-primary { color: var(--accent); border-color: var(--accent); }
        .btn-outline-primary:hover { background: var(--accent); border-color: var(--accent); }
        .btn-outline-info { color: #0891b2; border-color: #0891b2; }
        .btn-outline-info:hover { background: #0891b2; border-color: #0891b2; color: #fff; }
        .btn-outline-warning { color: #d97706; border-color: #d97706; }
        .btn-outline-warning:hover { background: #d97706; border-color: #d97706; color: #fff; }
        .btn-outline-danger { color: #dc2626; border-color: #dc2626; }
        .btn-outline-danger:hover { background: #dc2626; border-color: #dc2626; color: #fff; }
        .btn-outline-secondary { color: #64748b; border-color: #cbd5e1; }
        .btn-outline-secondary:hover { background: #64748b; border-color: #64748b; color: #fff; }

        /* ===== FORM ===== */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1px solid #e2e8f0;
            font-size: .8125rem;
            padding: .45rem .75rem;
            color: #1e293b;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-label {
            font-weight: 600;
            font-size: .8125rem;
            color: #334155;
            margin-bottom: .3rem;
        }

        /* ===== ALERTS ===== */
        .alert {
            border: none;
            border-radius: var(--radius-sm);
            font-size: .8125rem;
        }

        /* ===== PAGINATION ===== */
        .pagination {
            margin-bottom: 0;
            gap: 2px;
        }
        .page-link {
            border: none;
            border-radius: var(--radius-sm) !important;
            padding: .35rem .7rem;
            font-size: .8125rem;
            color: #475569;
            background: transparent;
            font-weight: 500;
        }
        .page-link:hover {
            background: #e2e8f0;
            color: #0f172a;
        }
        .page-item.active .page-link {
            background: var(--accent);
            color: #fff;
        }
        .page-item.disabled .page-link {
            color: #cbd5e1;
            background: transparent;
        }

        /* ===== TOGGLE MOBILE ===== */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.25rem;
            color: #475569;
            padding: .25rem;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1039;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 767.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-overlay.show { display: block; }
            .sidebar-toggle { display: inline-flex; }
            .main-wrapper { margin-left: 0; }
            .topbar { padding: .75rem 1rem; }
            .content-area { padding: 1rem; }
        }

        /* ===== PREDICTION / CHART extras (from dashboard) ===== */
        .chart-card {
            background: #fff;
            border-radius: var(--radius);
            box-shadow: var(--card-shadow);
            padding: 1.25rem;
            transition: box-shadow .2s;
        }
        .chart-card:hover { box-shadow: var(--card-shadow-hover); }
        .chart-card h3 {
            font-size: .9rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #0f172a;
        }
        .chart-container {
            position: relative;
            width: 100%;
        }
        .chart-container-sm { height: 240px; }
        .chart-container-md { height: 300px; }

        .status-list { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 1rem; }
        .status-item {
            font-size: .8rem;
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f8fafc;
            padding: .35rem .75rem;
            border-radius: 100px;
        }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dot-pending { background: #f59e0b; }
        .dot-progress { background: #3b82f6; }
        .dot-done { background: #10b981; }
        .dot-cancelled { background: #ef4444; }

        .pred-item {
            padding: .6rem .75rem;
            background: #f8fafc;
            border-radius: var(--radius-sm);
            border-left: 3px solid #7c3aed;
        }
        .pred-item:hover { background: #f1f5f9; }

        .activity-item {
            padding: .6rem .75rem;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            font-size: .8125rem;
        }
        .activity-item:last-child { border-bottom: none; }
        .activity-item .time {
            font-size: .7rem;
            color: #94a3b8;
        }

        .table-wrapper {
            overflow-x: auto;
        }
        .table-wrapper table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-wrapper th, .table-wrapper td {
            padding: .5rem .75rem;
            text-align: left;
            border-bottom: 1px solid rgba(0,0,0,0.04);
            font-size: .8125rem;
        }
        .table-wrapper th {
            font-weight: 600;
            color: #64748b;
            font-size: .75rem;
            text-transform: uppercase;
            letter-spacing: .03em;
            background: #f8fafc;
        }

        /* Page header inside content */
        .page-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .75rem;
            margin-bottom: 1.25rem;
        }
        .page-actions h5 {
            font-weight: 700;
            font-size: 1rem;
            color: #0f172a;
            margin: 0;
        }

        footer {
            margin-top: 2rem;
            padding: 1rem 0;
            color: #94a3b8;
            font-size: .75rem;
            border-top: 1px solid #e2e8f0;
            text-align: center;
        }

        /* ===== LOGO WATERMARK (semua halaman) ===== */
        .app-watermark {
            position: fixed;
            top: 50%;
            left: calc(50% + var(--sidebar-width) / 2);
            transform: translate(-50%, -50%);
            width: 550px;
            height: 550px;
            background-image: url('{{ asset("logo.webp") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.04;
            z-index: 0;
            pointer-events: none;
        }
    </style>
    @stack('styles')
</head>
<body>
    {{-- Logo Watermark Background --}}
    <div class="app-watermark"></div>

    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <img src="{{ asset('logo.webp') }}" alt="Logo">
            
            <span>BMW ULTIMA</span>
        </div>
        <div class="divider"></div>

        <div class="nav-section">Menu</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </a>
            </li>

            @can('manajer-or-office', auth()->user())
            <li class="nav-item">
                <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                    <i class="bi bi-people"></i> Pelanggan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('vehicles.index') }}" class="nav-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                    <i class="bi bi-car-front"></i> Kendaraan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('spare-parts.index') }}" class="nav-link {{ request()->routeIs('spare-parts.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam"></i> Spare Part
                </a>
            </li>
            @endcan

            <li class="nav-item">
                <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">
                    <i class="bi bi-wrench"></i> Service Orders
                </a>
            </li>

            @can('manajer-or-office', auth()->user())
            <li class="nav-item">
                <a href="{{ route('monitoring') }}" class="nav-link {{ request()->routeIs('monitoring') ? 'active' : '' }}">
                    <i class="bi bi-graph-up"></i> Monitoring
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('prediction') }}" class="nav-link {{ request()->routeIs('prediction') ? 'active' : '' }}">
                    <i class="bi bi-magic"></i> Prediksi
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('management.users.index') }}" class="nav-link {{ request()->routeIs('management.users.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge"></i> Manajemen User
                </a>
            </li>
            @endcan
        </ul>

        <div class="divider"></div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nav-link logout-btn">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </aside>

    {{-- Main --}}
    <div class="main-wrapper">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="sidebar-toggle" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
            </div>
            <div class="user-info">
                <span class="role-badge">{{ auth()->user()->role ?? 'guest' }}</span>
                <div class="avatar">
                    {{ substr(auth()->user()->name ?? 'G', 0, 1) }}
                </div>
                <span class="d-none d-sm-inline" style="font-weight:500;color:#1e293b;">{{ auth()->user()->name ?? 'Guest' }}</span>
            </div>
        </header>

        {{-- Content --}}
        <div class="content-area">
            {{-- Alert --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('open');
            document.getElementById('sidebarOverlay').classList.toggle('show');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    @stack('scripts')
</body>
</html>