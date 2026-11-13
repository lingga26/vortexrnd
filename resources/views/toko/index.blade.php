@extends('layouts.app')
@section('title', 'Data Toko')
@section('page-title', 'Iklan > Data Toko')

@section('content')

{{-- 
  💡 CSS DIKUNCI MATI DI DALAM KONTEN 
  Agar tabel melebar sempurna dan tema langsung terbaca oleh browser.
--}}
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
        width: 100% !important; min-width: 980px !important; display: table !important;
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
        background-color: rgba(160, 104, 65, 0.05) !important;
    }
    [data-theme="dark"] .table-vortex tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }
    .table-vortex th.text-left, .table-vortex td.text-left { text-align: left !important; }
    .table-vortex th.text-center, .table-vortex td.text-center { text-align: center !important; }
    .table-vortex th:first-child, .table-vortex td:first-child { padding-left: 20px !important; }
    .table-vortex th:last-child, .table-vortex td:last-child { padding-right: 20px !important; }

    /* Badge Kode Toko: Selalu 1 baris horizontal dan tidak patah ke bawah */
    .badge-vortex-kode {
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        white-space: nowrap !important;
        background: rgba(91, 33, 209, 0.10) !important;
        color: var(--purple) !important;
        padding: 6px 14px !important;
        border-radius: var(--radius-pill) !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: 0.4px !important;
        border: 1px solid rgba(91, 33, 209, 0.18) !important;
        line-height: 1.2 !important;
    }
    [data-theme="dark"] .badge-vortex-kode {
        background: rgba(147, 95, 255, 0.16) !important;
        color: #d6bcfa !important;
        border-color: rgba(147, 95, 255, 0.30) !important;
    }

    /* Badge Jam Operasional: Selalu 1 baris rapi dan proporsional */
    .badge-vortex-jam {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        white-space: nowrap !important;
        background: var(--badge-up-bg) !important;
        color: var(--badge-up-fg) !important;
        padding: 6px 12px !important;
        border-radius: var(--radius-pill) !important;
        font-size: 12px !important;
        font-weight: 800 !important;
        letter-spacing: 0.3px !important;
        border: 1px solid rgba(160, 104, 65, 0.22) !important;
        line-height: 1.2 !important;
    }
    [data-theme="dark"] .badge-vortex-jam {
        background: rgba(160, 104, 65, 0.22) !important;
        color: #DFCEBC !important;
        border-color: rgba(160, 104, 65, 0.35) !important;
    }
    .sim-card-meta {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 11px;
        font-weight: 700;
        color: var(--muted);
        white-space: nowrap;
        background: var(--track);
        padding: 3px 8px;
        border-radius: var(--radius-pill);
        margin-top: 4px;
    }
    .pagination-container {
        padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; 
        font-size: 13px; color: var(--muted); border-top: 1px solid var(--border);
        background: var(--bg-card);
    }
</style>

{{-- Toolbar Pencarian & Tambah Data --}}
<div class="toolbar-vortex">
    <a href="{{ route('toko.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Tambah Toko
    </a>
    <form class="search-form" method="GET" style="display: flex; gap: 8px; flex: 1; max-width: 400px;">
        @if(request('area'))
            <input type="hidden" name="area" value="{{ request('area') }}">
        @endif
        <input type="text" name="search" class="search-input form-control" 
               placeholder="Cari nama atau kode toko..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

@if(request('area'))
<div style="display: flex; gap: 8px; align-items: center; margin-bottom: 16px;">
    <span style="font-size: 12px; font-weight: 700; color: var(--text-muted);">Filter Area:</span>
    <span class="badge-vortex-cyan" style="font-size: 12px; font-weight: 800;">{{ request('area') }}</span>
    <a href="{{ route('toko.index') }}" style="font-size: 12px; font-weight: 800; color: #e74c3c; text-decoration: none; margin-left: 6px;">
        <i class="fas fa-times-circle"></i> Reset Filter
    </a>
</div>
@endif

{{-- Card Table Lapisan Luar --}}
<div class="vortex-card-index">
    <div class="table-responsive" style="overflow-x: auto; width: 100%;">
        <table class="table-vortex">
            <thead>
                <tr>
                    <th class="text-left" style="width: 50px;">No</th>
                    <th class="text-left" style="width: 20%;">Info Toko</th>
                    <th class="text-left" style="width: 15%;">Kode Toko</th>
                    <th class="text-left" style="width: 24%;">Lokasi & Kontak</th>
                    <th class="text-center" style="width: 16%;">Operasional</th>
                    <th class="text-left" style="width: 13%;">Keterangan</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($toko as $index => $item)
                <tr>
                    <td class="text-left" style="color: var(--muted); font-weight: 700;">
                        {{ $toko->firstItem() + $index }}
                    </td>
                    
                    {{-- 1. Info Toko (Nama Toko & Channel) --}}
                    <td class="text-left">
                        <span class="text-vortex-highlight" style="display: block; margin-bottom: 4px;">
                            {{ $item->nama_toko }}
                        </span>
                        <span class="badge-vortex-soft" title="Channel">
                            <i class="fas fa-tag mr-1" style="font-size: 10px;"></i> {{ $item->channel ?? '-' }}
                        </span>
                    </td>

                    {{-- 2. Kolom Khusus Kode Toko di Sebelah Info Toko --}}
                    <td class="text-left">
                        <span class="badge-vortex-kode" title="Kode Toko: {{ $item->kode_toko }}">
                            <i class="fas fa-store-alt" style="font-size: 11px;"></i>
                            <span>{{ $item->kode_toko ?? '-' }}</span>
                        </span>
                    </td>
                    
                    {{-- 3. Lokasi & Kontak Terpadu --}}
                    <td class="text-left">
                        <div style="font-weight: 800; color: var(--ink); margin-bottom: 4px;">
                            {{ $item->kota ?? '-' }}, {{ $item->provinsi ?? '-' }}
                        </div>
                        <div style="font-size: 12px; color: var(--muted); font-weight: 600; line-height: 1.4; margin-bottom: 6px; white-space: normal;">
                            {{ $item->alamat ?? '-' }}
                        </div>
                        <div style="font-size: 11px; color: var(--muted); display: flex; flex-direction: column; gap: 3px;">
                            <span><i class="fas fa-map-marker-alt text-danger mr-1"></i> Area: <strong>{{ $item->area ?? '-' }}</strong> (Teknisi: {{ $item->area_teknisi ?? '-' }})</span>
                            @if($item->telepon)
                                <span><i class="fas fa-phone-alt text-success mr-1"></i> {{ $item->telepon }}</span>
                            @endif
                        </div>
                    </td>

                    {{-- 4. Jam Operasional & SIM Card --}}
                    <td class="text-center">
                        <div style="display: inline-flex; flex-direction: column; align-items: center;">
                            @if(!empty($item->jam_buka) && !empty($item->jam_tutup))
                                <span class="badge-vortex-jam" title="Jam Operasional">
                                    <i class="far fa-clock" style="font-size: 11px;"></i>
                                    <span>{{ substr($item->jam_buka, 0, 5) }} - {{ substr($item->jam_tutup, 0, 5) }}</span>
                                </span>
                            @else
                                <span style="color: var(--muted); font-size: 13px; font-weight: 700;">-</span>
                            @endif

                            @if(!empty($item->sim_card) && $item->sim_card !== '-')
                                <div class="sim-card-meta" title="SIM Card">
                                    <i class="fas fa-sim-card" style="font-size: 10px;"></i>
                                    <span>{{ $item->sim_card }}</span>
                                </div>
                            @endif
                        </div>
                    </td>
                    
                    {{-- 5. Keterangan --}}
                    <td class="text-left">
                        @if($item->keterangan)
                            <div style="font-size: 12px; line-height: 1.4; color: var(--muted); display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $item->keterangan }}">
                                {{ $item->keterangan }}
                            </div>
                        @else
                            <span style="color: var(--muted);">-</span>
                        @endif
                    </td>
                    
                    {{-- 6. Tombol Aksi --}}
                    <td class="text-center">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('toko.edit', $item->id) }}" 
                               class="btn-icon-square btn-edit-vortex" 
                               title="Edit Data">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('toko.destroy', $item->id) }}" 
                                  method="POST" 
                                  style="display:inline;"
                                  onsubmit="return confirm('Peringatan: Apakah Anda yakin ingin menghapus data toko {{ $item->nama_toko }} secara permanen?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn-icon-square btn-delete-vortex" 
                                        title="Hapus Data">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 50px !important; color: var(--muted);">
                        <i class="fas fa-store-alt-slash" style="font-size: 48px; margin-bottom: 16px; display: block; opacity: 0.3;"></i>
                        <strong style="font-size: 15px;">Belum ada data toko yang tersimpan.</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- Pagination Section --}}
    @if(isset($toko) && $toko->hasPages())
    <div class="pagination-container">
        <span>Menampilkan <strong>{{ $toko->firstItem() ?? 0 }}</strong> - <strong>{{ $toko->lastItem() ?? 0 }}</strong> dari total <strong>{{ $toko->total() }}</strong> data toko</span>
        <div>{{ $toko->links() }}</div>
    </div>
    @endif
</div>

@endsection