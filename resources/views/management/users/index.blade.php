@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="container-fluid">
    <div class="page-actions">
        <h5><i class="bi bi-person-badge me-2"></i>Manajemen User</h5>
        @if(in_array(auth()->user()->role, ['manajer', 'office']))
        <a href="{{ route('management.users.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg"></i> Tambah User
        </a>
        @endif
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-indicator" style="background:var(--accent);"></div>
                <i class="bi bi-people stat-icon-bg"></i>
                <div class="stat-label">Total User</div>
                <div class="stat-value">{{ $userStats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-indicator" style="background:#10b981;"></div>
                <i class="bi bi-person-check stat-icon-bg"></i>
                <div class="stat-label">Manajer</div>
                <div class="stat-value">{{ $userStats['manajer'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-indicator" style="background:#0891b2;"></div>
                <i class="bi bi-person-badge stat-icon-bg"></i>
                <div class="stat-label">Office</div>
                <div class="stat-value">{{ $userStats['office'] }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card">
                <div class="stat-indicator" style="background:#f59e0b;"></div>
                <i class="bi bi-tools stat-icon-bg"></i>
                <div class="stat-label">Teknisi</div>
                <div class="stat-value">{{ $userStats['teknisi'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter & Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('management.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <select name="role" class="form-select">
                        <option value="">Semua Role</option>
                        <option value="manajer" {{ request('role') == 'manajer' ? 'selected' : '' }}>Manajer</option>
                        <option value="office" {{ request('role') == 'office' ? 'selected' : '' }}>Office</option>
                        <option value="teknisi" {{ request('role') == 'teknisi' ? 'selected' : '' }}>Teknisi</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <a href="{{ route('management.users.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'manajer')
                                    <span class="badge bg-success">Manajer</span>
                                @elseif($user->role === 'office')
                                    <span class="badge bg-info">Office</span>
                                @elseif($user->role === 'teknisi')
                                    <span class="badge bg-warning text-dark">Teknisi</span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('management.users.show', $user) }}" class="btn btn-sm btn-outline-info" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array(auth()->user()->role, ['manajer', 'office']) && auth()->id() !== $user->id)
                                    <a href="{{ route('management.users.edit', $user) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="{{ route('management.users.resetPassword', $user) }}" class="btn btn-sm btn-outline-secondary" title="Reset Password">
                                        <i class="bi bi-key"></i>
                                    </a>
                                    <form action="{{ route('management.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="empty-state">
                                    <i class="bi bi-people"></i>
                                    Belum ada data user.
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $users->links() }}
        </div>
    </div>
</div>
@endsection