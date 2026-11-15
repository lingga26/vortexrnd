@extends('layouts.app')
@section('title', 'Digital Signage')
@section('page-title', 'Iklan > Digital Signage')



@section('content')

{{-- CSS inline di dalam konten (layout tidak punya @stack('styles')) --}}
<style>
    .vortex-card-index {
        background: var(--bg-card) !important;
        border-radius: var(--radius-card) !important;
        box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important;
        overflow: hidden;
        width: 100%;
        display: block;
        margin-top: 10px;
    }
    .toolbar-vortex {
        display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; justify-content: space-between; align-items: center;
    }
    .table-vortex {
        width: 100% !important; min-width: 900px !important; display: table !important;
        border-collapse: collapse !important; table-layout: fixed !important; margin: 0 !important;
    }
    .table-vortex th {
        background-color: var(--bg-card) !important;
        color: var(--muted) !important;
        font-weight: 800 !important;
        border-bottom: 1px solid var(--border) !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.8px !important;
        padding: 16px 14px !important;
        vertical-align: middle !important;
    }
    .table-vortex td {
        padding: 16px 14px !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--ink) !important;
        font-weight: 600 !important;
        font-size: 0.88rem !important;
        vertical-align: middle !important;
    }
    .table-vortex tbody tr:hover td {
        background-color: rgba(37, 99, 235, 0.04) !important;
    }
    [data-theme="dark"] .table-vortex tbody tr:hover td {
        background-color: rgba(34, 211, 238, 0.05) !important;
    }
    .table-vortex th:first-child, .table-vortex td:first-child { padding-left: 20px !important; }
    .table-vortex th:last-child, .table-vortex td:last-child { padding-right: 20px !important; }
</style>

<div class="toolbar-vortex">
    <a href="{{ route('signage.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Tambah Signage
    </a>
    <form class="search-form" method="GET" style="display: flex; gap: 8px; flex: 1; max-width: 650px; flex-wrap: wrap; justify-content: flex-end;">
        @if(request('area'))
            <input type="hidden" name="area" value="{{ request('area') }}">
        @endif
        @if(request('baru_aktivasi'))
            <input type="hidden" name="baru_aktivasi" value="{{ request('baru_aktivasi') }}">
        @endif
        <select name="status_konten" class="form-control" onchange="this.form.submit()" style="max-width: 200px;">
            <option value="">-- Semua Status Konten --</option>
            @foreach(['Sudah Terbaru', 'Belum Terbaru', 'Tidak Diketahui'] as $sk)
                <option value="{{ $sk }}" {{ request('status_konten') === $sk ? 'selected' : '' }}>{{ $sk }}</option>
            @endforeach
        </select>
        <input type="text" name="search" class="search-input form-control" style="max-width: 260px;"
               placeholder="Cari ID, nama, atau area..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

@if(request()->anyFilled(['area', 'status_konten', 'baru_aktivasi', 'search']))
<div style="display: flex; gap: 8px; align-items: center; flex-wrap: wrap; margin-bottom: 16px;">
    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">Filter Aktif:</span>
    @if(request('area'))
        <span class="badge-vortex-cyan">Area: {{ request('area') }}</span>
    @endif
    @if(request('status_konten'))
        <span class="{{ request('status_konten') === 'Sudah Terbaru' ? 'badge-vortex-green' : 'badge-vortex-soft' }}" style="{{ request('status_konten') === 'Belum Terbaru' ? 'background: var(--badge-warn-bg); color: var(--badge-warn-fg);' : '' }}">
            Status: {{ request('status_konten') }}
        </span>
    @endif
    @if(request('baru_aktivasi'))
        <span class="badge-vortex-green">Baru Aktivasi (30 Hari Terakhir)</span>
    @endif
    @if(request('search'))
        <span class="badge-vortex-soft">Pencarian: "{{ request('search') }}"</span>
    @endif
    <a href="{{ route('signage.index') }}" style="font-size: 12px; font-weight: 800; color: #ef4444; text-decoration: none; margin-left: 6px;">
        <i class="fas fa-times-circle"></i> Reset Filter
    </a>
</div>
@endif

<div class="vortex-card-index">
    <div class="table-responsive" style="width: 100%;">
        <table class="table-vortex">
            <thead>
                <tr>
                    <th style="width: 25%;">Info Perangkat</th>
                    <th style="width: 20%;">Toko Penempatan</th>
                    <th style="width: 15%;">Model TV</th>
                    <th style="width: 15%;">Status Konten</th>
                    <th style="width: 10%;">Aktivasi</th>
                    <th style="width: 15%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($signages as $item)
                <tr>
                    <td>
                        <strong style="color: var(--ink); font-size: 14px;">{{ $item->nama_signage }}</strong><br>
                        <small style="color: var(--text-muted);">ID: {{ $item->device_id }}</small><br>
                        @if($item->isOnline())
                            <span class="badge-vortex-green"><i class="fas fa-wifi mr-1"></i> Online</span>
                        @else
                            <span class="badge-vortex-soft"><i class="fas fa-wifi mr-1"></i> Offline</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 700;">{{ $item->toko->nama_toko ?? '-' }}</div>
                        <small style="color: var(--text-muted);">{{ $item->toko->kode_toko ?? '' }}{{ !empty($item->toko->area) ? ' • ' . $item->toko->area : '' }}</small>
                    </td>
                    <td><span class="badge-vortex-cyan">{{ $item->modelSignage->nama_model_tv ?? '-' }}</span></td>
                    <td>
                        @if(($item->status_konten ?? '') == 'Sudah Terbaru')
                            <span class="badge-vortex-green">Sudah Terbaru</span>
                        @else
                            <span class="badge-vortex-soft" style="background: var(--badge-warn-bg); color: var(--badge-warn-fg);">{{ $item->status_konten ?? 'Belum Terbaru' }}</span>
                        @endif
                    </td>
                    <td>{{ $item->tanggal_aktivasi ? $item->tanggal_aktivasi->format('d/m/Y') : '-' }}</td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('signage.edit', $item) }}" class="btn-icon-square btn-edit-vortex"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('signage.destroy', $item) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus signage ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-square btn-delete-vortex"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-desktop" style="font-size: 30px; opacity: 0.3; display: block; margin-bottom: 10px;"></i>
                        Belum ada data signage.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 24px;">{{ $signages->links() }}</div>
</div>

@endsection