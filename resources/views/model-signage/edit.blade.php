@extends('layouts.app')
@section('title', 'Edit Model Signage')
@section('page-title', 'Data Master > Model Signage > Edit')

@section('content')

{{-- 
  💡 CSS DIKUNCI MATI DI DALAM KONTEN
  Agar 100% kembar identik dengan halaman Tambah.
--}}
<style>
    .vortex-card-edit {
        background: var(--bg-card) !important;
        border-radius: var(--radius-card) !important;
        box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important;
        overflow: hidden;
        width: 100%;
        max-width: 800px;
        margin-top: 10px;
    }

    .vortex-header-edit {
        padding: 24px 24px 16px 24px;
        border-bottom: 1px solid var(--border) !important;
        display: flex;
        align-items: center;
    }

    .vortex-header-edit h5 {
        margin: 0;
        font-weight: 800;
        color: var(--ink) !important;
        font-size: 1.25rem;
        display: flex;
        align-items: center;
    }

    .icon-title-box {
        width: 42px; height: 42px; 
        background: var(--accent-soft) !important; 
        color: var(--accent) !important; 
        border-radius: 12px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        margin-right: 16px;
    }

    .vortex-body-edit { padding: 30px 24px; }

    .vortex-label-locked {
        font-size: 11px;
        font-weight: 800;
        color: var(--muted) !important;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        margin-bottom: 8px;
        display: block;
    }

    .vortex-input-locked {
        width: 100% !important;
        display: block !important;
        padding: 13px 16px;
        border: 1px solid var(--border) !important;
        border-radius: 12px !important;
        font-size: 14px;
        font-weight: 600;
        font-family: inherit;
        background: var(--bg-card) !important;
        color: var(--ink) !important;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .vortex-input-locked:focus {
        outline: none;
        border-color: var(--accent) !important;
        box-shadow: 0 0 0 3px var(--accent-soft) !important;
        background: var(--bg-card) !important;
    }

    .vortex-btn-locked {
        padding: 12px 28px;
        border-radius: var(--radius-pill) !important;
        font-weight: 700;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-save-v { background: var(--accent) !important; color: var(--btn-primary-fg, #ffffff) !important; box-shadow: 0 4px 12px var(--accent-soft) !important; }
    .btn-save-v:hover { background: var(--accent-hover) !important; transform: translateY(-2px); color: var(--btn-primary-fg, #ffffff) !important; }

    .btn-cancel-v { background: var(--track) !important; color: var(--ink) !important; border: 1px solid var(--border) !important; }
    .btn-cancel-v:hover { background: var(--border) !important; transform: translateY(-2px); color: var(--ink) !important; }

    .vortex-error-text { color: #ef4444; font-size: 12px; font-weight: 700; margin-top: 6px; display: block; }
</style>

<div class="vortex-card-edit">
    <div class="vortex-header-edit">
        <h5>
            <div class="icon-title-box">
                <i class="fas fa-edit" style="font-size: 16px;"></i>
            </div>
            Ubah Data Model Signage
        </h5>
    </div>
    
    <div class="vortex-body-edit">
        <form action="{{ route('model-signage.update', $modelSignage) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 24px;">
                <label for="nama_model" class="vortex-label-locked">
                    Nama Model TV / Perangkat <span style="color: #e74c3c; font-size: 14px; margin-left: 2px;">*</span>
                </label>
                <input type="text" 
                       name="nama_model" 
                       id="nama_model" 
                       class="vortex-input-locked" 
                       value="{{ old('nama_model', $modelSignage->nama_model) }}" 
                       placeholder="Contoh: QLED 55 Inch Ultra" 
                       required>
                @error('nama_model')
                    <span class="vortex-error-text"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> {{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="merk" class="vortex-label-locked">Merk / Pabrikan</label>
                <input type="text" 
                       name="merk" 
                       id="merk" 
                       class="vortex-input-locked" 
                       value="{{ old('merk', $modelSignage->merk) }}"
                       placeholder="Contoh: Samsung / LG / Polytron">
                @error('merk')
                    <span class="vortex-error-text"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> {{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="resolusi" class="vortex-label-locked">Resolusi Layar</label>
                <input type="text" 
                       name="resolusi" 
                       id="resolusi" 
                       class="vortex-input-locked" 
                       value="{{ old('resolusi', $modelSignage->resolusi) }}"
                       placeholder="Contoh: 1920x1080 atau 1080x1920">
                @error('resolusi')
                    <span class="vortex-error-text"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> {{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 24px;">
                <label for="orientasi" class="vortex-label-locked">Orientasi Layar</label>
                <select name="orientasi" id="orientasi" class="vortex-input-locked">
                    <option value="landscape" {{ old('orientasi', $modelSignage->orientasi) == 'landscape' ? 'selected' : '' }}>Landscape (Horizontal)</option>
                    <option value="portrait" {{ old('orientasi', $modelSignage->orientasi) == 'portrait' ? 'selected' : '' }}>Portrait (Vertikal)</option>
                </select>
                @error('orientasi')
                    <span class="vortex-error-text"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> {{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 12px;">
                <label for="keterangan" class="vortex-label-locked">Keterangan Tambahan</label>
                <textarea name="keterangan" 
                          id="keterangan" 
                          class="vortex-input-locked" 
                          rows="5"
                          placeholder="Deskripsi spesifikasi tambahan (opsional)...">{{ old('keterangan', $modelSignage->keterangan) }}</textarea>
                @error('keterangan')
                    <span class="vortex-error-text"><i class="fas fa-exclamation-circle" style="margin-right: 4px;"></i> {{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 12px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--border) !important;">
                <button type="submit" class="vortex-btn-locked btn-save-v">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <a href="{{ route('model-signage.index') }}" class="vortex-btn-locked btn-cancel-v">
                    <i class="fas fa-times"></i> Batalkan
                </a>
            </div>
        </form>
    </div>
</div>
@endsection