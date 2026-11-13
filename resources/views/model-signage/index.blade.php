@extends('layouts.app')
@section('title', 'Model Signage')
@section('page-title', 'Data Master > Model Signage')

@section('content')

<style>
    .vortex-card-index {
        background: var(--bg-card) !important;
        border-radius: var(--radius-card) !important;
        box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important;
        width: 100% !important; 
        margin-top: 10px;
        overflow: hidden;
    }
    .toolbar-vortex {
        display: flex; gap: 12px; margin-bottom: 24px; flex-wrap: wrap; justify-content: space-between; align-items: center;
    }
    .table-responsive {
        width: 100% !important;
        display: block;
        overflow-x: auto;
    }
    .table-vortex {
        width: 100% !important; 
        min-width: 100% !important;
        border-collapse: collapse !important;
        table-layout: auto !important;
    }
    .table-vortex th {
        background-color: var(--bg-card) !important;
        color: var(--muted) !important;
        font-weight: 800 !important;
        border-bottom: 1px solid var(--border) !important;
        text-transform: uppercase !important;
        font-size: 0.75rem !important;
        letter-spacing: 0.8px !important;
        padding: 16px 24px !important;
        vertical-align: middle !important;
        text-align: left;
    }
    .table-vortex td {
        padding: 16px 24px !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--ink) !important;
        font-weight: 600 !important;
        font-size: 0.88rem !important;
        vertical-align: middle !important;
        text-align: left;
    }
    .table-vortex tbody tr:hover td {
        background-color: rgba(160, 104, 65, 0.05) !important;
    }
    [data-theme="dark"] .table-vortex tbody tr:hover td {
        background-color: rgba(255, 255, 255, 0.03) !important;
    }
    .col-aksi { width: 120px !important; text-align: center !important; }
</style>

<div class="toolbar-vortex">
    <a href="{{ route('model-signage.create') }}" class="btn btn-primary">
        <i class="fas fa-plus mr-2"></i> Tambah Model
    </a>
    <form method="GET" style="display: flex; gap: 8px; flex: 1; max-width: 400px;">
        <input type="text" name="search" class="form-control" placeholder="Cari model TV..." value="{{ request('search') }}">
        <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i></button>
    </form>
</div>

<div class="vortex-card-index">
    <div class="table-responsive">
        <table class="table-vortex">
            <thead>
                <tr>
                    <th style="width: 50px;">No</th>
                    <th>Nama Model Perangkat</th>
                    <th>Merk</th>
                    <th>Resolusi & Orientasi</th>
                    <th>Keterangan</th>
                    <th class="col-aksi">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($models as $index => $item)
                <tr>
                    <td style="color: var(--text-muted);">{{ $models->firstItem() + $index }}</td>
                    <td><strong style="color: var(--color-primary);">{{ $item->nama_model }}</strong></td>
                    <td><span class="badge-vortex-soft">{{ $item->merk ?? '-' }}</span></td>
                    <td>
                        <div style="display: flex; flex-direction: column; gap: 6px; align-items: flex-start;">
                            <span class="badge-vortex-cyan">
                                <i class="fas fa-tv mr-1"></i> {{ $item->resolusi ?? '1920x1080' }}
                            </span>
                            <span class="badge-vortex-cyan" style="background-color: rgba(54, 185, 204, 0.06); color: #5bc0be;">
                                <i class="fas fa-retweet mr-1"></i> {{ $item->orientasi ?? 'landscape' }}
                            </span>
                        </div>
                    </td>
                    <td style="color: var(--text-muted);">{{ $item->keterangan ?? '-' }}</td>
                    <td class="col-aksi">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('model-signage.edit', $item) }}" class="btn-icon-square btn-edit-vortex"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('model-signage.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-square btn-delete-vortex"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center" style="padding: 40px;">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($models->hasPages())
    <div style="padding: 16px 24px; border-top: 1px solid var(--border-main);">
        {{ $models->links() }}
    </div>
    @endif
</div>
@endsection