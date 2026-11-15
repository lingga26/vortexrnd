@extends('layouts.app')
@section('title', 'Distribusi Jadwal Tayang')
@section('page-title', 'Iklan > Distribusi Jadwal Tayang')

@section('content')

{{-- 💡 CSS DIKUNCI MATI AGAR 100% KONSISTEN DENGAN MODUL LAIN --}}
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
        width: 100% !important; min-width: 950px !important; border-collapse: collapse !important;
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
    }
    .table-vortex tbody tr:hover td {
        background-color: rgba(37, 99, 235, 0.04) !important;
    }
    [data-theme="dark"] .table-vortex tbody tr:hover td {
        background-color: rgba(34, 211, 238, 0.05) !important;
    }
</style>

<div class="toolbar-vortex">
    <a href="{{ route('distribusi.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Tambah Distribusi
    </a>
    <form method="GET" style="display: flex; gap: 8px; flex: 1; max-width: 400px;">
        <input type="text" name="search" class="form-control" placeholder="Cari area, toko, signage..." value="{{ request('search') }}">
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
                    <th style="width: 10%;">Nama Area</th>
                    <th style="width: 14%;">Nama Toko</th>
                    <th style="width: 18%;">Digital Signage</th>
                    <th style="width: 9%;">Mulai</th>
                    <th style="width: 9%;">Selesai</th>
                    <th style="width: 12%;">Waktu Tayang</th>
                    <th style="width: 20%;">Jadwal Tayang</th>
                    <th style="width: 8%; text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($distribusi as $group)
                @php
                    $first   = $group->first();
                    $signage = $first->digitalSignage;
                    $toko    = $signage?->toko;
                    $perJam  = $group->sortBy('id')->groupBy(fn ($d) => substr($d->waktu_mulai, 0, 5) . ' - ' . substr($d->waktu_selesai, 0, 5))->sortKeys();
                    $no = 0;
                @endphp
                <tr>
                    <td>{{ strtoupper($toko?->area ?? '-') }}</td>
                    <td>{{ strtoupper($toko?->nama_toko ?? '-') }}</td>
                    <td>
                        <div style="font-weight: 800; color: var(--ink);">{{ strtoupper($signage?->nama_signage ?? '-') }}</div>
                        <small style="color: var(--text-muted);">{{ $signage?->device_id }}</small>
                    </td>
                    <td>{{ \Carbon\Carbon::parse($first->tanggal_mulai)->format('d-m-Y') }}</td>
                    <td>{{ \Carbon\Carbon::parse($first->tanggal_selesai)->format('d-m-Y') }}</td>
                    <td>
                        <div>{{ $first->frekuensi_hari ?? 'Setiap Hari' }}</div>
                        <div>{{ $first->frekuensi_tanggal ?? 'Semua Tanggal' }}</div>
                        <div>{{ $first->frekuensi_bulan ?? 'Setiap Bulan' }}</div>
                    </td>
                    <td>
                        @foreach ($perJam as $jam => $items)
                            <div style="font-weight: 700; margin-top: {{ $loop->first ? 0 : 8 }}px;">&bull; {{ $jam }}</div>
                            @foreach ($items as $d)
                                @php $no++; @endphp
                                <div>#{{ $no }}: {{ $d->jadwalTayang?->nama_jadwal ?? '-' }}
                                    @if(!$d->aktif) <span class="badge-vortex-soft">Nonaktif</span> @endif
                                </div>
                            @endforeach
                        @endforeach
                    </td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('distribusi.edit', $first->id) }}" class="btn-icon-square btn-edit-vortex"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('distribusi.destroy', $first->id) }}" method="POST" onsubmit="return confirm('Hapus distribusi ini beserta semua jadwal tayangnya?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-square btn-delete-vortex"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center" style="padding: 40px; color: var(--text-muted);">Belum ada data distribusi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding: 16px 24px;">{{ $distribusi->links() }}</div>
</div>
@endsection