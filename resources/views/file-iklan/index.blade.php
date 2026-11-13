@extends('layouts.app')
@section('title', 'File Iklan')
@section('page-title', 'Iklan > File Iklan')

@section('content')

{{-- 💡 CSS DIKUNCI MATI DI DALAM KONTEN AGAR 100% KEMBAR DENGAN MODUL LAIN --}}
<style>
    .vortex-card-index {
        background: var(--bg-card) !important;
        border-radius: var(--radius-card) !important;
        box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important;
        overflow: hidden;
        width: 100%;
        margin-top: 10px;
    }
    .toolbar-vortex { display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; justify-content: space-between; align-items: center; }
    
    .table-vortex {
        width: 100% !important; min-width: 980px !important; border-collapse: collapse !important;
        table-layout: fixed !important; margin: 0 !important;
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
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
    }
    .table-vortex tbody tr:hover td {
        background-color: rgba(160, 104, 65, 0.05) !important;
    }
    [data-theme="dark"] .table-vortex tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }

    /* Badges Tipe Iklan */
    .badge-vortex-content {
        background: rgba(56, 189, 248, 0.14) !important;
        color: #0284c7 !important;
        font-weight: 800 !important;
        border-radius: var(--radius-pill) !important;
        padding: 4px 10px !important;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        border: 1px solid rgba(56, 189, 248, 0.25) !important;
    }
    .badge-vortex-bumper {
        background: rgba(147, 51, 234, 0.14) !important;
        color: #9333ea !important;
        font-weight: 800 !important;
        border-radius: var(--radius-pill) !important;
        padding: 4px 10px !important;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        border: 1px solid rgba(147, 51, 234, 0.25) !important;
    }
    .badge-vortex-promo {
        background: rgba(245, 158, 11, 0.14) !important;
        color: #d97706 !important;
        font-weight: 800 !important;
        border-radius: var(--radius-pill) !important;
        padding: 4px 10px !important;
        font-size: 11px !important;
        letter-spacing: 0.5px !important;
        white-space: nowrap !important;
        display: inline-flex !important;
        border: 1px solid rgba(245, 158, 11, 0.25) !important;
    }
    [data-theme="dark"] .badge-vortex-content {
        background: rgba(56, 189, 248, 0.18) !important;
        color: #38bdf8 !important;
        border-color: rgba(56, 189, 248, 0.35) !important;
    }
    [data-theme="dark"] .badge-vortex-bumper {
        background: rgba(168, 85, 247, 0.18) !important;
        color: #c084fc !important;
        border-color: rgba(168, 85, 247, 0.35) !important;
    }
    [data-theme="dark"] .badge-vortex-promo {
        background: rgba(251, 191, 36, 0.18) !important;
        color: #fbbf24 !important;
        border-color: rgba(251, 191, 36, 0.35) !important;
    }

    /* Tombol Bulat Preview */
    .btn-play-preview {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: rgba(56, 189, 248, 0.14);
        color: #0284c7;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.18s ease;
        border: 1px solid rgba(56, 189, 248, 0.25);
    }
    .btn-play-preview:hover {
        background: #0284c7;
        color: #FFFFFF;
        transform: scale(1.08);
    }
    [data-theme="dark"] .btn-play-preview {
        background: rgba(56, 189, 248, 0.18);
        color: #38bdf8;
        border-color: rgba(56, 189, 248, 0.35);
    }
    [data-theme="dark"] .btn-play-preview:hover {
        background: #38bdf8;
        color: #000000;
    }
</style>

<div class="toolbar-vortex">
    <a href="{{ route('file-iklan.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Tambah File Iklan
    </a>
    <form method="GET" style="display: flex; gap: 8px; flex: 1; max-width: 500px;">
        <select name="tipe" class="form-control" style="width: auto; border-radius: 12px;">
            <option value="">Semua Tipe</option>
            <option value="CONTENT" {{ request('tipe') == 'CONTENT' ? 'selected' : '' }}>CONTENT</option>
            <option value="BUMPER" {{ request('tipe') == 'BUMPER' ? 'selected' : '' }}>BUMPER</option>
            <option value="PROMO" {{ request('tipe') == 'PROMO' ? 'selected' : '' }}>PROMO</option>
        </select>
        <input type="text" name="search" class="form-control" placeholder="Cari..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="vortex-card-index">
    <div class="table-responsive">
        <table class="table-vortex">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 15%;">Pelanggan</th>
                    <th style="width: 95px;">Tipe</th>
                    <th style="width: 22%;">Nama Iklan</th>
                    <th style="width: 75px; text-align: center;">Preview</th>
                    <th style="width: 100px;">Detail</th>
                    <th style="width: 22%;">Catatan</th>
                    <th style="width: 95px; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($fileIklan as $index => $item)
                <tr>
                    <td>{{ $fileIklan->firstItem() + $index }}</td>
                    <td style="font-weight: 800; color: #f06292; word-break: break-word;">{{ $item->pelanggan }}</td>
                    <td>
                        @if($item->tipe == 'CONTENT') <span class="badge-vortex-content">CONTENT</span>
                        @elseif($item->tipe == 'BUMPER') <span class="badge-vortex-bumper">BUMPER</span>
                        @else <span class="badge-vortex-promo">PROMO</span> @endif
                    </td>
                    <td style="word-break: break-word; font-weight: 700;">{{ $item->nama_iklan }}</td>
                    <td class="text-center">
                        @if($item->file_path)
                            <a href="{{ asset('media/' . $item->file_path) }}" target="_blank" class="btn-play-preview" title="Putar Media">
                                <i class="fas fa-play" style="font-size: 11px; margin-left: 2px;"></i>
                            </a>
                        @else
                            <span style="color: var(--muted); opacity: 0.6;">-</span>
                        @endif
                    </td>
                    <td>
                        <small style="display: block; font-weight: 700;">{{ $item->ukuran_bytes ? round($item->ukuran_bytes / 1024 / 1024, 2) . ' MB' : '-' }}</small>
                        @if(!empty($item->durasi_detik))
                            <small style="display: block; color: var(--muted); font-weight: 600;">{{ $item->durasi_detik }} Detik</small>
                        @endif
                    </td>
                    <td style="color: var(--ink); font-size: 13px; line-height: 1.4; word-break: break-word; overflow-wrap: anywhere;">
                        @if(!empty($item->catatan))
                            <div style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; word-break: break-word; overflow-wrap: anywhere;" title="{{ $item->catatan }}">
                                {{ $item->catatan }}
                            </div>
                        @else
                            <span style="color: var(--muted); opacity: 0.6;">-</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('file-iklan.edit', $item->id) }}" class="btn-icon-square btn-edit-vortex"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('file-iklan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus file ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-square btn-delete-vortex"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center" style="padding: 40px;">Belum ada data.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 24px;">{{ $fileIklan->links() }}</div>
</div>
@endsection