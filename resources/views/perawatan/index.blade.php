@extends('layouts.admin')
@section('title', 'Perawatan Media')
@section('page-title', 'Perawatan Media')

@section('content')

<style>
    .pm-card { background: var(--bg-card) !important; border: 1px solid var(--border) !important; border-radius: var(--radius-card) !important; padding: 24px; margin-bottom: 20px; box-shadow: var(--shadow-card) !important; }
    .pm-card h3 { font-size: 1.05rem; font-weight: 800; margin-bottom: 8px; color: var(--ink) !important; display: flex; align-items: center; gap: 8px; }
    .pm-card p { color: var(--muted) !important; font-size: 0.88rem; margin-bottom: 16px; line-height: 1.5; }
    .pm-btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border: none; border-radius: var(--radius-pill); font-weight: 700; font-size: 0.85rem; cursor: pointer; color: #fff; margin: 0 8px 8px 0; transition: all 0.2s ease; }
    .pm-aman { background: var(--accent) !important; color: var(--btn-primary-fg, #fff) !important; box-shadow: 0 4px 12px var(--accent-soft); }
    .pm-bahaya { background: #ef4444; }
    .pm-lokasi { width: 100%; border-collapse: collapse; font-size: 0.88rem; }
    .pm-lokasi td { padding: 12px 14px; border-bottom: 1px solid var(--border) !important; color: var(--ink) !important; word-break: break-all; }
    .pm-ok { color: var(--badge-up-fg); font-weight: 800; }
    .pm-no { color: var(--badge-down-fg); font-weight: 800; }
    .pm-out { background: var(--bg-card); color: var(--ink); border: 1px solid var(--border); padding: 18px; border-radius: 14px; font-family: Consolas, monospace; font-size: 0.85rem; white-space: pre-wrap; word-break: break-all; max-height: 480px; overflow: auto; }
</style>

<div class="pm-card">
    <h3><i class="fas fa-folder-open"></i> Lokasi penyimpanan</h3>
    <table class="pm-lokasi">
        @foreach($lokasi as $l)
        <tr>
            <td style="width:240px; font-weight:700;">{{ $l['nama'] }}</td>
            <td>{{ $l['path'] ?: '-' }}</td>
            <td style="width:150px;">
                @if(!$l['ada']) <span class="pm-no">tidak ditemukan</span>
                @elseif($l['bisa_tulis']) <span class="pm-ok">bisa ditulis</span>
                @else <span class="pm-no">tidak bisa ditulis</span> @endif
            </td>
        </tr>
        @endforeach
    </table>
</div>

<div class="pm-card">
    <h3><i class="fas fa-trash-can"></i> File yatim (iklan terhapus, file masih ada di server)</h3>
    <p>
        Membandingkan file di server dengan data iklan di database. Jalankan <b>Cek</b> dulu untuk melihat daftarnya,
        lalu <b>Hapus</b> bila daftarnya sudah benar. File yang diubah kurang dari 60 menit lalu dilewati.
    </p>
    <form method="POST" action="{{ route('perawatan.jalankan') }}" style="display:inline;">
        @csrf <input type="hidden" name="aksi" value="yatim-cek">
        <button class="pm-btn pm-aman" type="submit"><i class="fas fa-magnifying-glass"></i> Cek file yatim</button>
    </form>
    <form method="POST" action="{{ route('perawatan.jalankan') }}" style="display:inline;"
          onsubmit="return confirm('Hapus semua file yatim dari server? Tindakan ini tidak bisa dibatalkan.');">
        @csrf <input type="hidden" name="aksi" value="yatim-hapus">
        <button class="pm-btn pm-bahaya" type="submit"><i class="fas fa-trash"></i> Hapus file yatim</button>
    </form>
</div>

<div class="pm-card">
    <h3><i class="fas fa-right-left"></i> Pindahkan folder media ke nama subdomain</h3>
    <p>
        Memindahkan media client dari folder lama <code>{client_id}/iklan</code> ke <code>{subdomain}/iklan</code>
        (contoh: <code>kalibrasi/iklan</code>), memperbarui path di database, lalu mendistribusikan ulang jadwal
        agar URL video baru sampai ke TV. Sebaiknya backup folder media dan tabel <code>file_iklan</code> lebih dulu.
    </p>
    <form method="POST" action="{{ route('perawatan.jalankan') }}" style="display:inline;">
        @csrf <input type="hidden" name="aksi" value="pindah-cek">
        <button class="pm-btn pm-aman" type="submit"><i class="fas fa-list-check"></i> Cek rencana pemindahan</button>
    </form>
    <form method="POST" action="{{ route('perawatan.jalankan') }}" style="display:inline;"
          onsubmit="return confirm('Jalankan pemindahan folder media sekarang?');">
        @csrf <input type="hidden" name="aksi" value="pindah-jalankan">
        <button class="pm-btn pm-bahaya" type="submit"><i class="fas fa-play"></i> Jalankan pemindahan</button>
    </form>
</div>

@if($hasil)
<div class="pm-card">
    <h3>
        Hasil: {{ $judul[$hasil['aksi']] ?? $hasil['aksi'] }}
        @if($hasil['kode'] === 0) <span class="pm-ok">(selesai)</span> @else <span class="pm-no">(ada error)</span> @endif
    </h3>
    <div class="pm-out">{{ $hasil['output'] }}</div>
</div>
@endif

@endsection
