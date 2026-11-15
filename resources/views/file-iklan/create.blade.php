@extends('layouts.app')
@section('title', 'Tambah File Iklan')
@section('page-title', 'Iklan > File Iklan > Tambah File')

@section('content')

{{-- 💡 CSS DIKUNCI MATI AGAR 100% KONSISTEN DENGAN FORM LAIN --}}
<style>
    .vortex-card-form {
        background: var(--bg-card) !important; border-radius: var(--radius-card) !important; box-shadow: var(--shadow-card) !important;
        border: 1px solid var(--border) !important; overflow: hidden; width: 100%; max-width: 900px; margin-top: 10px;
    }
    .vortex-header-form { padding: 24px 24px 16px 24px; border-bottom: 1px solid var(--border) !important; display: flex; align-items: center; }
    .vortex-header-form h5 { margin: 0; font-weight: 800; color: var(--ink) !important; font-size: 1.25rem; display: flex; align-items: center; }
    .icon-title-box { width: 42px; height: 42px; background: var(--accent-soft) !important; color: var(--accent) !important; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 16px; }
    .vortex-body-form { padding: 30px 24px; }
    .vortex-label-locked { font-size: 11px; font-weight: 800; color: var(--muted) !important; text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px; display: block; }
    .vortex-input-locked { width: 100% !important; display: block !important; padding: 13px 16px; border: 1px solid var(--border) !important; border-radius: 12px !important; font-size: 14px; font-weight: 600; background: var(--bg-card) !important; color: var(--ink) !important; transition: all 0.2s ease; box-sizing: border-box; }
    .vortex-input-locked:focus { outline: none; border-color: var(--accent) !important; box-shadow: 0 0 0 3px var(--accent-soft) !important; background: var(--bg-card) !important; }
    .vortex-error-text { color: #ef4444; font-size: 12px; font-weight: 700; margin-top: 6px; display: block; }
    
    .vortex-btn-locked { padding: 12px 28px; border-radius: var(--radius-pill) !important; font-weight: 700; font-size: 13px; text-transform: uppercase; border: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.2s ease; cursor: pointer; text-decoration: none; }
    .btn-save-v { background: var(--accent) !important; color: var(--btn-primary-fg, #ffffff) !important; box-shadow: 0 4px 12px var(--accent-soft) !important; }
    .btn-save-v:hover { background: var(--accent-hover) !important; transform: translateY(-2px); color: var(--btn-primary-fg, #ffffff) !important; }
    .btn-cancel-v { background: var(--track) !important; color: var(--ink) !important; border: 1px solid var(--border) !important; }
    .btn-cancel-v:hover { background: var(--border) !important; transform: translateY(-2px); color: var(--ink); }
    
    .vortex-form-row { display: flex; gap: 20px; flex-wrap: wrap; margin-bottom: 24px; width: 100%; }
    .vortex-form-col { flex: 1; min-width: 250px; }
    .upload-info { font-size: 11px; color: var(--muted); margin-top: 8px; display: block; font-weight: 600; }
</style>

<div class="vortex-card-form">
    <div class="vortex-header-form">
        <h5>
            <div class="icon-title-box"><i class="fas fa-film" style="font-size: 16px;"></i></div>
            Form Tambah File Iklan Baru
        </h5>
    </div>
    
    <div class="vortex-body-form">
        <form method="POST" action="{{ route('file-iklan.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="vortex-form-row">
                {{-- Pelanggan --}}
                <div class="vortex-form-col">
                    <label class="vortex-label-locked">Pelanggan / Brand <span style="color: #e74c3c;">*</span></label>
                    <input type="text" name="pelanggan" class="vortex-input-locked @error('pelanggan') is-invalid @enderror" 
                           value="{{ old('pelanggan') }}" placeholder="Masukkan nama pelanggan" required>
                    @error('pelanggan') <span class="vortex-error-text">{{ $message }}</span> @enderror
                </div>

                {{-- Tipe --}}
                <div class="vortex-form-col">
                    <label class="vortex-label-locked">Tipe File <span style="color: #e74c3c;">*</span></label>
                    <select name="tipe" class="vortex-input-locked @error('tipe') is-invalid @enderror" required>
                        @foreach(['CONTENT','BUMPER','PROMO'] as $t)
                            <option value="{{ $t }}" {{ old('tipe', 'CONTENT') === $t ? 'selected' : '' }}>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('tipe') <span class="vortex-error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            {{-- Nama Iklan --}}
            <div style="margin-bottom: 24px;">
                <label class="vortex-label-locked">Nama Iklan <span style="color: #e74c3c;">*</span></label>
                <input type="text" name="nama_iklan" class="vortex-input-locked @error('nama_iklan') is-invalid @enderror" 
                       value="{{ old('nama_iklan') }}" placeholder="Contoh: Iklan Promo Lebaran 2026" required>
                @error('nama_iklan') <span class="vortex-error-text">{{ $message }}</span> @enderror
            </div>

            {{-- Upload File --}}
            <div style="margin-bottom: 24px;">
                <label class="vortex-label-locked">Upload File Video <span style="color: #e74c3c;">*</span></label>
                <input type="file" name="file_video" class="vortex-input-locked @error('file_video') is-invalid @enderror" 
                       accept="video/mp4,video/avi,video/quicktime,video/x-matroska" required 
                       style="padding: 10px;">
                <span class="upload-info">
                    <i class="fas fa-info-circle mr-1"></i> Format: MP4, AVI, MOV, MKV. Ukuran Maksimal: 500 MB.
                </span>
                @error('file_video') <span class="vortex-error-text">{{ $message }}</span> @enderror
            </div>

            {{-- Catatan --}}
            <div style="margin-bottom: 12px;">
                <label class="vortex-label-locked">Catatan</label>
                <textarea name="catatan" class="vortex-input-locked" rows="3" 
                          placeholder="Tambahkan catatan khusus mengenai file iklan ini (opsional)...">{{ old('catatan', old('keterangan')) }}</textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div style="display: flex; gap: 12px; margin-top: 36px; padding-top: 24px; border-top: 1px solid var(--border-main, #e9ecef);">
                <button type="submit" class="vortex-btn-locked btn-save-v">
                    <i class="fas fa-save"></i> Simpan File Iklan
                </button>
                <a href="{{ route('file-iklan.index') }}" class="vortex-btn-locked btn-cancel-v">
                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection