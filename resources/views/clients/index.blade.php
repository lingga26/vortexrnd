@extends('layouts.admin')
@section('title', 'Client')
@section('page-title', 'Kelola Client')

@section('content')

<style>
    .vortex-card-index {
        background: var(--bg-card) !important; border-radius: var(--radius-card) !important; box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important; overflow: hidden; width: 100%; margin-top: 10px;
    }
    .toolbar-vortex { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; justify-content: space-between; align-items: center; }

    .table-vortex {
        width: 100% !important; min-width: 100% !important; border-collapse: collapse !important;
        table-layout: fixed !important; margin: 0 !important;
    }
    .table-vortex th {
        background-color: var(--bg-card) !important; color: var(--muted) !important;
        font-weight: 800 !important; border-bottom: 1px solid var(--border) !important;
        text-transform: uppercase !important; font-size: 0.75rem !important; letter-spacing: 0.8px !important;
        padding: 16px 16px !important; vertical-align: middle !important;
    }
    .table-vortex td {
        padding: 14px 16px !important; border-bottom: 1px solid var(--border) !important;
        color: var(--ink) !important; font-weight: 600 !important; font-size: 0.85rem !important;
    }
    .table-vortex tbody tr:hover td { background-color: rgba(37, 99, 235, 0.04) !important; }

    .btn-icon-square {
        width: 36px; height: 36px; padding: 0; display: inline-flex; align-items: center; justify-content: center;
        border-radius: var(--radius-pill) !important; font-size: 14px; transition: all 0.2s; border: none; text-decoration: none;
    }
    .btn-edit-vortex { background-color: var(--badge-warn-bg) !important; color: var(--badge-warn-fg) !important; }
    .btn-edit-vortex:hover { background-color: #D97706 !important; color: #fff !important; transform: translateY(-2px); }
    .btn-delete-vortex { background-color: var(--badge-down-bg) !important; color: var(--badge-down-fg) !important; }
    .btn-delete-vortex:hover { background-color: #DC2626 !important; color: #fff !important; transform: translateY(-2px); }
    .btn-user-vortex { background-color: var(--accent-soft) !important; color: var(--accent) !important; }
    .btn-user-vortex:hover { background-color: var(--accent) !important; color: var(--btn-primary-fg, #fff) !important; transform: translateY(-2px); }
    .btn-hapus-vortex { background-color: var(--badge-down-bg) !important; color: var(--badge-down-fg) !important; cursor: pointer; }
    .btn-hapus-vortex:hover { background-color: #DC2626 !important; color: #fff !important; transform: translateY(-2px); }
    .link-subdomain { color: var(--accent); font-weight: 700; text-decoration: none; word-break: break-all; }
    
    .badge-vortex-green { background-color: var(--badge-up-bg); color: var(--badge-up-fg); padding: 5px 12px; border-radius: var(--radius-pill); font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(22, 163, 74, 0.25); }
    .badge-vortex-soft { background-color: var(--badge-down-bg); color: var(--badge-down-fg); padding: 5px 12px; border-radius: var(--radius-pill); font-weight: 700; font-size: 0.75rem; border: 1px solid rgba(220, 38, 38, 0.25); }
</style>

<div class="toolbar-vortex">
    <a href="{{ route('clients.create') }}" class="btn" style="background-color: var(--accent); color: var(--btn-primary-fg, #ffffff); border: none; padding: 12px 24px; border-radius: var(--radius-pill); font-weight: 700; box-shadow: 0 4px 12px var(--accent-soft);">
        <i class="fas fa-plus mr-1"></i> Tambah Client
    </a>
</div>

@if(session('success'))<div class="alert alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger" style="margin-bottom:16px;">{{ session('error') }}</div>@endif

<div class="vortex-card-index">
    <div class="table-responsive">
        <table class="table-vortex">
            <thead>
                <tr>
                    <th>ID</th><th>Kode</th><th>Nama Client</th><th>Subdomain</th>
                    <th style="text-align:center;">User</th><th style="text-align:center;">Toko</th><th style="text-align:center;">Signage</th>
                    <th>Status</th><th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($clients as $c)
                <tr>
                    <td>{{ $c->id }}</td>
                    <td>{{ $c->kode }}</td>
                    <td><strong>{{ $c->nama }}</strong></td>
                    <td>@if($c->subdomain)<a href="{{ $c->url() }}" target="_blank" rel="noopener" class="link-subdomain">{{ $c->subdomain }}</a>@else<span style="color: var(--text-muted);">-</span>@endif</td>
                    <td style="text-align:center;">{{ $users[$c->id] ?? 0 }}</td>
                    <td style="text-align:center;">{{ $toko[$c->id] ?? 0 }}</td>
                    <td style="text-align:center;">{{ $signage[$c->id] ?? 0 }}</td>
                    <td>@if($c->aktif)<span class="badge-vortex-green">Aktif</span>@else<span class="badge-vortex-soft">Nonaktif</span>@endif</td>
                    <td style="text-align:center;">
                        <div style="display:inline-flex; gap:6px; align-items:center;">
                        <a href="{{ route('clients.users', $c->id) }}" class="btn-icon-square btn-user-vortex" title="Kelola user &amp; reset password"><i class="fas fa-users-cog"></i></a>
                        @if($c->id != 1)
                        <form action="{{ route('clients.toggle', $c->id) }}" method="POST" onsubmit="return confirm('Ubah status client ini? Semua user client akan {{ $c->aktif ? 'tidak bisa login' : 'bisa login lagi' }}.')">
                            @csrf @method('PATCH')
                            <button type="submit" class="btn-icon-square {{ $c->aktif ? 'btn-delete-vortex' : 'btn-edit-vortex' }}" title="{{ $c->aktif ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="fas {{ $c->aktif ? 'fa-ban' : 'fa-check' }}"></i>
                            </button>
                        </form>
                        <form action="{{ route('clients.destroy', $c->id) }}" method="POST" data-kode="{{ $c->kode }}" data-nama="{{ $c->nama }}"
                              onsubmit="var k = prompt('PERINGATAN: client ' + this.dataset.nama + ' beserta SEMUA data (user, toko, signage, jadwal, distribusi, video) akan dihapus PERMANEN dan tidak bisa dikembalikan.\n\nKetik kode client \'' + this.dataset.kode + '\' untuk melanjutkan:'); if (k === null) { return false; } this.konfirmasi.value = k; return true;">
                            @csrf @method('DELETE')
                            <input type="hidden" name="konfirmasi" value="">
                            <button type="submit" class="btn-icon-square btn-hapus-vortex" title="Hapus client permanen"><i class="fas fa-trash"></i></button>
                        </form>
                        @else <small style="color: var(--text-muted);">default</small> @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
