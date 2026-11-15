@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'WELCOME TO VORTEXSIGN DASHBOARD')

@section('content')

<style>
    /* ==========================================================================
       ✨ DASHBOARD STYLES (LIGHT & DARK MODE)
       ========================================================================== */
    .dashboard-header-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--ink);
        letter-spacing: -0.3px;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    /* 📊 1. KARTU STATISTIK (5 KARTU) */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
        width: 100%;
        min-width: 0;
        box-sizing: border-box;
    }

    .stat-card-item {
        position: relative;
        padding: 20px 22px;
        border-radius: var(--radius-card);
        border: 1px solid var(--border);
        box-shadow: var(--shadow-card);
        text-decoration: none !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 140px;
        min-width: 0;
        box-sizing: border-box;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .stat-card-item:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    }

    /* Gradient Pastel Light Mode */
    .stat-card-lavender { background: var(--stat-lavender); }
    .stat-card-blue     { background: var(--stat-blue); }
    .stat-card-green    { background: var(--stat-green); }
    .stat-card-amber    { background: var(--stat-amber); }
    .stat-card-red      { background: var(--stat-red); }

    /* Dark Mode: Kartu 1 ungu gelap, kartu 2-5 hitam pekat (#151516) */
    [data-theme="dark"] .stat-card-blue,
    [data-theme="dark"] .stat-card-green,
    [data-theme="dark"] .stat-card-amber,
    [data-theme="dark"] .stat-card-red {
        background: var(--bg-card) !important;
    }

    /* Header Baris Atas Kartu: Ikon kiri, Tombol panah kanan */
    .stat-card-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }
    .stat-icon-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 1px 3px rgba(17, 28, 48, 0.08);
        color: #16233A;
    }
    [data-theme="dark"] .stat-icon-circle {
        background: rgba(255, 255, 255, 0.08);
        color: #22D3EE;
    }

    .icon-tone-purple,
    .icon-tone-blue,
    .icon-tone-green,
    .icon-tone-amber {
        color: #16233A !important;
    }
    .icon-tone-red {
        color: #DC2626 !important;
    }
    [data-theme="dark"] .icon-tone-purple,
    [data-theme="dark"] .icon-tone-blue,
    [data-theme="dark"] .icon-tone-green,
    [data-theme="dark"] .icon-tone-amber {
        color: #22D3EE !important;
    }
    [data-theme="dark"] .icon-tone-red {
        color: #F87171 !important;
    }

    /* Tombol Aksi Bulat (↗) */
    .stat-card-arrow-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: var(--stat-arrow-bg, #2563EB);
        color: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.35);
        transition: transform 0.2s ease;
    }
    .stat-card-red .stat-card-arrow-btn {
        background: var(--stat-arrow-red-bg, #DC2626);
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.35);
    }
    [data-theme="dark"] .stat-card-arrow-btn {
        color: #06202B;
        box-shadow: 0 2px 6px rgba(34, 211, 238, 0.35);
    }
    [data-theme="dark"] .stat-card-red .stat-card-arrow-btn {
        color: #FFFFFF;
        box-shadow: 0 2px 6px rgba(248, 113, 113, 0.35);
    }
    .stat-card-item:hover .stat-card-arrow-btn {
        transform: rotate(45deg);
    }

    /* Baris Bawah Kartu: Label & Angka */
    .stat-card-bottom {
        margin-top: 18px;
    }
    .stat-card-label {
        font-size: 12.5px;
        font-weight: 600;
        color: var(--muted);
        letter-spacing: 0.2px;
    }
    .stat-card-number {
        font-size: 30px;
        font-weight: 800;
        color: var(--ink);
        line-height: 1.1;
        margin-top: 4px;
        letter-spacing: -0.5px;
    }

    /* 📈 2. ROW GRAFIK (3 KARTU) */
    .charts-row-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 24px;
        width: 100%;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
    }

    .chart-box-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: 22px 24px;
        display: flex;
        flex-direction: column;
        min-height: 340px;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
        box-sizing: border-box;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    .chart-box-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }
    .chart-box-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .chart-box-title i {
        color: var(--accent);
        font-size: 14px;
    }
    .chart-more-btn {
        background: transparent;
        border: none;
        color: var(--muted);
        font-size: 14px;
        cursor: pointer;
        padding: 4px 8px;
        border-radius: 6px;
        transition: background-color 0.15s ease;
    }
    .chart-more-btn:hover {
        background: var(--track);
        color: var(--ink);
    }

    /* Layout Khusus Chart Donut Bersegmen */
    .segmented-chart-layout {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex: 1;
        gap: 12px;
        position: relative;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }
    .donut-legend-container {
        display: flex;
        flex-direction: column;
        gap: 10px;
        min-width: 75px;
        flex-shrink: 0;
    }
    .donut-legend-item {
        display: flex;
        flex-direction: column;
    }
    .legend-dot-val {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .legend-color-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }
    .legend-pct-val {
        font-size: 14px;
        font-weight: 800;
        color: var(--ink);
        line-height: 1.2;
    }
    .legend-label-sub {
        font-size: 11px;
        color: var(--muted);
        font-weight: 600;
        margin-left: 18px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 95px;
    }

    /* Wrap Ring SVG Donut Bersegmen */
    .donut-ring-wrap {
        position: relative;
        width: 160px;
        height: 160px;
        max-width: 160px;
        max-height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin: auto;
    }
    .donut-ring-svg {
        width: 100%;
        height: 100%;
        transform: rotate(0deg);
    }
    .donut-center-badge {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        pointer-events: none;
    }
    .donut-center-badge .badge-sub {
        font-size: 10.5px;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .donut-center-badge .badge-val {
        font-size: 24px;
        font-weight: 800;
        color: var(--ink);
        line-height: 1.1;
        margin-top: 2px;
    }

    /* Wrap Bar Charts (Clean Container for Chart.js) */
    .chart-bar-body {
        position: relative;
        width: 100%;
        height: 220px;
        max-height: 220px;
        min-width: 0;
        max-width: 100%;
        overflow: hidden;
    }
    .chart-bar-body canvas {
        display: block;
        width: 100% !important;
        max-width: 100% !important;
        height: 100% !important;
        max-height: 220px !important;
    }

    /* 📋 3. TABEL REKAP AREA */
    .rekap-card-table {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        overflow: hidden;
        width: 100%;
    }
    .rekap-header-bar {
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid var(--border);
    }
    .rekap-header-title {
        font-size: 15px;
        font-weight: 800;
        color: var(--ink);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .rekap-header-title i {
        color: var(--accent);
    }
    .rekap-live-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border-radius: var(--radius-pill);
        background: var(--track);
        color: var(--muted);
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
    }
    .rekap-live-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: var(--accent);
        box-shadow: 0 0 0 2px var(--accent-soft);
    }

    /* Badges Angka di Tabel */
    .rekap-badge-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        padding: 5px 14px;
        border-radius: var(--radius-pill);
        font-size: 13px;
        font-weight: 700;
        text-decoration: none !important;
        transition: transform 0.15s ease, opacity 0.15s ease;
    }
    .rekap-badge-link:hover {
        transform: translateY(-1px);
        opacity: 0.9;
    }
    .rekap-pill-green  { background: var(--badge-up-bg); color: var(--badge-up-fg); }
    .rekap-pill-orange { background: var(--badge-warn-bg); color: var(--badge-warn-fg); }
    .rekap-pill-red    { background: var(--badge-down-bg); color: var(--badge-down-fg); }
    .rekap-pill-track  { background: var(--track); color: var(--ink); }

    .area-name-link {
        color: var(--ink) !important;
        font-weight: 700;
        text-decoration: none;
        transition: color 0.15s ease;
    }
    .area-name-link:hover {
        color: var(--accent) !important;
    }

    /* Responsive Breakpoints */
    @media (max-width: 1200px) {
        .stat-cards-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
        .charts-row-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 768px) {
        .stat-cards-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .charts-row-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
    @media (max-width: 480px) {
        .stat-cards-grid {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<!-- Jembatan Data untuk JS -->
<div id="vortex-data-bridge" style="display: none;"
     data-toko-labels="{{ json_encode($chartData->pluck('area')) }}"
     data-toko-values="{{ json_encode($chartData->pluck('total')) }}"
     data-sudah-update="{{ (int)$sudahUpdate }}"
     data-belum-update="{{ (int)$belumUpdate }}"
     data-tidak-diketahui="{{ (int)$tidakDiketahui }}"
     data-online="{{ (int)$signageOnline }}"
     data-offline="{{ (int)$signageOffline }}"
     data-total-toko="{{ (int)$totalToko }}">
</div>

<div class="dashboard-header-title">
    WELCOME TO VORTEXSIGN DASHBOARD
</div>

{{-- 📊 1. ROW STATISTIK (5 KARTU DENGAN GRADIENT PASTEL & TAMPILAN SESUAI REFERENSI) --}}
<div class="stat-cards-grid">
    <!-- 1. Total Toko -->
    <a href="{{ route('toko.index') }}" class="stat-card-item stat-card-lavender" title="Lihat Semua Toko">
        <div class="stat-card-top">
            <div class="stat-icon-circle icon-tone-purple">
                <i class="fas fa-store"></i>
            </div>
            <div class="stat-card-arrow-btn">
                <i class="fas fa-arrow-up" style="transform: rotate(45deg);"></i>
            </div>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-card-label">Total Toko</div>
            <div class="stat-card-number">{{ $totalToko }}</div>
        </div>
    </a>

    <!-- 2. Total Signage -->
    <a href="{{ route('signage.index') }}" class="stat-card-item stat-card-blue" title="Lihat Semua Signage">
        <div class="stat-card-top">
            <div class="stat-icon-circle icon-tone-blue">
                <i class="fas fa-desktop"></i>
            </div>
            <div class="stat-card-arrow-btn">
                <i class="fas fa-arrow-up" style="transform: rotate(45deg);"></i>
            </div>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-card-label">Total Signage</div>
            <div class="stat-card-number">{{ $totalSignage }}</div>
        </div>
    </a>

    <!-- 3. Sudah Update -->
    <a href="{{ route('signage.index', ['status_konten' => 'Sudah Terbaru']) }}" class="stat-card-item stat-card-green" title="Lihat Signage yang Sudah Update">
        <div class="stat-card-top">
            <div class="stat-icon-circle icon-tone-green">
                <i class="fas fa-sync-alt"></i>
            </div>
            <div class="stat-card-arrow-btn">
                <i class="fas fa-arrow-up" style="transform: rotate(45deg);"></i>
            </div>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-card-label">Sudah Update</div>
            <div class="stat-card-number">{{ $sudahUpdate }}</div>
        </div>
    </a>

    <!-- 4. Belum Update -->
    <a href="{{ route('signage.index', ['status_konten' => 'Belum Terbaru']) }}" class="stat-card-item stat-card-amber" title="Lihat Signage yang Belum Update">
        <div class="stat-card-top">
            <div class="stat-icon-circle icon-tone-amber">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-card-arrow-btn">
                <i class="fas fa-arrow-up" style="transform: rotate(45deg);"></i>
            </div>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-card-label">Belum Update</div>
            <div class="stat-card-number">{{ $belumUpdate }}</div>
        </div>
    </a>

    <!-- 5. Tidak Diketahui -->
    <a href="{{ route('signage.index', ['status_konten' => 'Tidak Diketahui']) }}" class="stat-card-item stat-card-red" title="Lihat Signage dengan Status Tidak Diketahui">
        <div class="stat-card-top">
            <div class="stat-icon-circle icon-tone-red">
                <i class="fas fa-question-circle"></i>
            </div>
            <div class="stat-card-arrow-btn">
                <i class="fas fa-arrow-up" style="transform: rotate(45deg);"></i>
            </div>
        </div>
        <div class="stat-card-bottom">
            <div class="stat-card-label">Tidak Diketahui</div>
            <div class="stat-card-number">{{ $tidakDiketahui ?? 0 }}</div>
        </div>
    </a>
</div>

{{-- 📈 2. ROW GRAFIK (DONUT BERSEGMEN & PILL BAR CHARTS) --}}
<div class="charts-row-grid">
    <!-- Card 1: Distribusi Toko Area (Donut Bersegmen Sesuai Gambar 1 & 2) -->
    <div class="chart-box-card">
        <div class="chart-box-header">
            <div class="chart-box-title">
                <i class="fas fa-map-marked-alt"></i> Distribusi Toko Area
            </div>
            <button class="chart-more-btn" type="button" title="Opsi"><i class="fas fa-ellipsis-h"></i></button>
        </div>
        <div class="segmented-chart-layout">
            <!-- Legend Kiri -->
            <div class="donut-legend-container" id="areaLegendContainer"></div>
            <!-- Ring Donut Bersegmen SVG -->
            <div class="donut-ring-wrap">
                <svg id="areaSegmentedSvg" viewBox="0 0 240 240" class="donut-ring-svg"></svg>
                <div class="donut-center-badge">
                    <span class="badge-sub">Total Toko</span>
                    <span class="badge-val">{{ $totalToko }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Status Update Konten (Pill Bar Chart) -->
    <div class="chart-box-card">
        <div class="chart-box-header">
            <div class="chart-box-title">
                <i class="fas fa-photo-video"></i> Status Update Konten
            </div>
            <button class="chart-more-btn" type="button" title="Opsi"><i class="fas fa-ellipsis-h"></i></button>
        </div>
        <div class="chart-bar-body">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <!-- Card 3: Status Koneksi STB (Pill Bar Chart) -->
    <div class="chart-box-card">
        <div class="chart-box-header">
            <div class="chart-box-title">
                <i class="fas fa-wifi"></i> Status Koneksi STB
            </div>
            <button class="chart-more-btn" type="button" title="Opsi"><i class="fas fa-ellipsis-h"></i></button>
        </div>
        <div class="chart-bar-body">
            <canvas id="koneksiChart"></canvas>
        </div>
    </div>
</div>

{{-- 📋 3. TABEL REKAP AREA --}}
<div class="rekap-card-table">
    <div class="rekap-header-bar">
        <div class="rekap-header-title">
            <i class="fas fa-table"></i> Rekap Status per Area
        </div>
        <div class="rekap-live-pill">
            <span class="rekap-live-dot"></span> MONITOR: LIVE SYNC
        </div>
    </div>

    <div style="width: 100%; overflow-x: auto;">
        <table class="table-vortex" style="width: 100%; table-layout: fixed; border-collapse: collapse; margin: 0;">
            <thead>
                <tr>
                    <th style="width: 26%; text-align: left; padding: 16px 24px;">Nama Area</th>
                    <th style="width: 15%; text-align: center; padding: 16px 10px;">Update Berhasil</th>
                    <th style="width: 14%; text-align: center; padding: 16px 10px;">Total Toko</th>
                    <th style="width: 15%; text-align: center; padding: 16px 10px;">Belum Terbaru</th>
                    <th style="width: 15%; text-align: center; padding: 16px 10px;">Tidak Diketahui</th>
                    <th style="width: 15%; text-align: right; padding: 16px 24px;">Baru Aktivasi</th>
                </tr>
            </thead>
            <tbody>
                <?php $totalBaruAktivasi = 0; ?>
                <?php if (isset($statsPerArea) && count($statsPerArea) > 0) : ?>
                    <?php foreach ($statsPerArea as $stat) : ?>
                        <?php 
                            $row = (array) $stat; 
                            $area = $row['area'] ?? '-';
                            $sudah = (int) ($row['sudah_update'] ?? 0);
                            $totalRow = (int) ($row['total_toko'] ?? 0);
                            $belum = (int) ($row['belum_update'] ?? 0);
                            $unknown = (int) ($row['tidak_diketahui'] ?? 0);
                            $baru = (int) ($row['baru_aktivasi'] ?? 0);
                            $totalBaruAktivasi += $baru;
                        ?>
                        <tr>
                            <td style="text-align: left; padding: 16px 24px;">
                                <a href="{{ route('signage.index', ['area' => $area]) }}" class="area-name-link" title="Lihat semua signage di area {{ $area }}">
                                    {{ $area }}
                                </a>
                            </td>
                            <td style="text-align: center; padding: 16px 10px;">
                                <a href="{{ route('signage.index', ['area' => $area, 'status_konten' => 'Sudah Terbaru']) }}" class="rekap-badge-link rekap-pill-green" title="Lihat signage Sudah Terbaru di area {{ $area }}">
                                    <i class="fas fa-check-circle" style="font-size: 11px;"></i> {{ $sudah }}
                                </a>
                            </td>
                            <td style="text-align: center; padding: 16px 10px;">
                                <a href="{{ route('toko.index', ['area' => $area]) }}" class="rekap-badge-link rekap-pill-track" title="Lihat daftar toko di area {{ $area }}">
                                    {{ $totalRow }}
                                </a>
                            </td>
                            <td style="text-align: center; padding: 16px 10px;">
                                <a href="{{ route('signage.index', ['area' => $area, 'status_konten' => 'Belum Terbaru']) }}" class="rekap-badge-link rekap-pill-orange" title="Lihat signage Belum Terbaru di area {{ $area }}">
                                    {{ $belum }}
                                </a>
                            </td>
                            <td style="text-align: center; padding: 16px 10px;">
                                <a href="{{ route('signage.index', ['area' => $area, 'status_konten' => 'Tidak Diketahui']) }}" class="rekap-badge-link rekap-pill-track" style="color: var(--muted);" title="Lihat signage Status Tidak Diketahui di area {{ $area }}">
                                    {{ $unknown }}
                                </a>
                            </td>
                            <td style="text-align: right; padding: 16px 24px;">
                                <a href="{{ route('signage.index', ['area' => $area, 'baru_aktivasi' => 1]) }}" class="rekap-badge-link rekap-pill-green" title="Lihat signage Baru Aktivasi (30 hari terakhir) di area {{ $area }}">
                                    +{{ $baru }}
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--muted); padding: 32px;">
                            Belum ada data area yang terdaftar.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr style="border-top: 2px solid var(--border); font-weight: 800;">
                    <td style="text-align: left; padding: 16px 24px;">
                        <a href="{{ route('signage.index') }}" class="area-name-link" title="Lihat Semua Signage">GLOBAL TOTAL</a>
                    </td>
                    <td style="text-align: center; padding: 16px 10px;">
                        <a href="{{ route('signage.index', ['status_konten' => 'Sudah Terbaru']) }}" class="rekap-badge-link rekap-pill-green" title="Lihat Semua Signage Sudah Terbaru">
                            <i class="fas fa-check-circle" style="font-size: 11px;"></i> {{ $sudahUpdate }}
                        </a>
                    </td>
                    <td style="text-align: center; padding: 16px 10px;">
                        <a href="{{ route('toko.index') }}" class="rekap-badge-link rekap-pill-track" title="Lihat Semua Toko">
                            {{ $totalToko }}
                        </a>
                    </td>
                    <td style="text-align: center; padding: 16px 10px;">
                        <a href="{{ route('signage.index', ['status_konten' => 'Belum Terbaru']) }}" class="rekap-badge-link rekap-pill-orange" title="Lihat Semua Signage Belum Terbaru">
                            {{ $belumUpdate }}
                        </a>
                    </td>
                    <td style="text-align: center; padding: 16px 10px;">
                        <a href="{{ route('signage.index', ['status_konten' => 'Tidak Diketahui']) }}" class="rekap-badge-link rekap-pill-track" style="color: var(--muted);" title="Lihat Semua Signage Status Tidak Diketahui">
                            {{ $tidakDiketahui ?? 0 }}
                        </a>
                    </td>
                    <td style="text-align: right; padding: 16px 24px;">
                        <a href="{{ route('signage.index', ['baru_aktivasi' => 1]) }}" class="rekap-badge-link rekap-pill-green" title="Lihat Semua Signage Baru Aktivasi">
                            +{{ $totalBaruAktivasi }}
                        </a>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
(function() {
    const bridge = document.getElementById('vortex-data-bridge');
    if (!bridge) return;

    const areaLabels = JSON.parse(bridge.getAttribute('data-toko-labels') || '[]');
    const areaValues = JSON.parse(bridge.getAttribute('data-toko-values') || '[]');
    const sudahUpdate = parseInt(bridge.getAttribute('data-sudah-update') || 0);
    const belumUpdate = parseInt(bridge.getAttribute('data-belum-update') || 0);
    const tidakDiketahui = parseInt(bridge.getAttribute('data-tidak-diketahui') || 0);
    const signageOnline = parseInt(bridge.getAttribute('data-online') || 0);
    const signageOffline = parseInt(bridge.getAttribute('data-offline') || 0);
    const totalToko = parseInt(bridge.getAttribute('data-total-toko') || 0);

    // Helper deteksi tema
    function isDarkTheme() {
        return (document.documentElement.getAttribute('data-theme') === 'dark');
    }

    /* ==========================================================================
       🔘 1. RENDER SEGMENTED DONUT (DISTRIBUSI TOKO AREA)
       ========================================================================== */
    function renderSegmentedRing() {
        const svg = document.getElementById('areaSegmentedSvg');
        const legendContainer = document.getElementById('areaLegendContainer');
        if (!svg) return;

        svg.innerHTML = '';
        if (legendContainer) legendContainer.innerHTML = '';

        const dark = isDarkTheme();
        const colorTrack   = dark ? '#101F35' : '#DBE2EE';

        // 🎨 Palet warna kategori berbeda kontras tinggi agar setiap daerah jelas berbeda warnanya
        const paletteLight = [
            '#2563EB', // Biru Royal
            '#F59E0B', // Amber / Oranye
            '#10B981', // Hijau Emerald
            '#8B5CF6', // Ungu Violet
            '#F43F5E', // Merah Koral
            '#06B6D4', // Biru Cyan
            '#EC4899', // Pink Fuchsia
            '#6366F1'  // Biru Indigo
        ];

        const paletteDark = [
            '#38BDF8', // Sky Blue
            '#FBBF24', // Amber Terang
            '#34D399', // Mint / Emerald Terang
            '#A78BFA', // Ungu Terang
            '#FB7185', // Rose Terang
            '#22D3EE', // Cyan Terang
            '#F472B6', // Pink Terang
            '#818CF8'  // Indigo Terang
        ];

        const palette = dark ? paletteDark : paletteLight;
        const totalSegments = 72; // Rapat membentuk cincin donat utuh
        const total = totalToko > 0 ? totalToko : areaValues.reduce((a, b) => a + b, 0);

        // Alokasi warna segmen
        let segmentColors = [];
        if (total === 0 || areaValues.length === 0) {
            for (let i = 0; i < totalSegments; i++) segmentColors.push(colorTrack);
        } else {
            let allocated = 0;
            areaValues.forEach((val, idx) => {
                if (val <= 0) return;
                const count = Math.max(1, Math.round((val / total) * totalSegments));
                const col = palette[idx % palette.length];
                for (let c = 0; c < count && segmentColors.length < totalSegments; c++) {
                    segmentColors.push(col);
                    allocated++;
                }
            });
            // Genapkan segmen jika pembulatan kurang dari totalSegments
            if (segmentColors.length > 0 && segmentColors.length < totalSegments) {
                const lastCol = segmentColors[segmentColors.length - 1];
                while (segmentColors.length < totalSegments) {
                    segmentColors.push(lastCol);
                }
            }
        }

        // Gambar Donut Berkelanjutan Halus
        const cx = 120, cy = 120;
        const rInner = 74, rOuter = 106;

        for (let i = 0; i < totalSegments; i++) {
            const angleDeg = (i * (360 / totalSegments)) - 90;
            const rad = (angleDeg * Math.PI) / 180;
            const x1 = cx + rInner * Math.cos(rad);
            const y1 = cy + rInner * Math.sin(rad);
            const x2 = cx + rOuter * Math.cos(rad);
            const y2 = cy + rOuter * Math.sin(rad);

            const line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
            line.setAttribute('x1', x1.toFixed(2));
            line.setAttribute('y1', y1.toFixed(2));
            line.setAttribute('x2', x2.toFixed(2));
            line.setAttribute('y2', y2.toFixed(2));
            line.setAttribute('stroke', segmentColors[i]);
            line.setAttribute('stroke-width', '11');
            line.setAttribute('stroke-linecap', 'butt');
            svg.appendChild(line);
        }

        // Render Legend di Sisi Kiri
        if (legendContainer) {
            if (areaLabels.length === 0) {
                legendContainer.innerHTML = '<div style="color:var(--muted); font-size:11px;">Belum ada data toko</div>';
            } else {
                areaLabels.forEach((label, idx) => {
                    const val = areaValues[idx] || 0;
                    const pct = total > 0 ? Math.round((val / total) * 100) : 0;
                    const col = palette[idx % palette.length];

                    const item = document.createElement('div');
                    item.className = 'donut-legend-item';
                    item.innerHTML = `
                        <div class="legend-dot-val">
                            <span class="legend-color-dot" style="background:${col};"></span>
                            <span class="legend-pct-val">${pct}%</span>
                        </div>
                        <div class="legend-label-sub" title="${label}">${label}</div>
                    `;
                    legendContainer.appendChild(item);
                });
            }
        }
    }

    /* ==========================================================================
       📊 2. RENDER BAR PILL CHARTS (STATUS UPDATE & KONEKSI STB)
       ========================================================================== */
    let chartStatusInstance = null;
    let chartKoneksiInstance = null;

    function renderBarCharts() {
        const dark = isDarkTheme();
        const colorMuted = dark ? '#8CA3BF' : '#64748B';
        const colorBorder = dark ? '#27405F' : '#DBE2EE';
        const colorTooltipBg = dark ? '#0A1628' : '#16233A';

        // 🎨 Palet Warna Status Sesuai Ketentuan Baru:
        // Sudah Update: hijau #16A34A (terang) / #34D399 (gelap)
        const colorSudah      = dark ? '#34D399' : '#16A34A';
        const colorSudahHover = dark ? '#10B981' : '#15803D';

        // Belum Update: kuning/amber #D97706 (terang) / #FBBF24 (gelap)
        const colorBelum      = dark ? '#FBBF24' : '#D97706';
        const colorBelumHover = dark ? '#F59E0B' : '#B45309';

        // Tidak Diketahui: abu #64748B (terang) / #8CA3BF (gelap)
        const colorTidak      = dark ? '#8CA3BF' : '#64748B';
        const colorTidakHover = dark ? '#94A3B8' : '#475569';

        // Online: hijau #16A34A (terang) / #34D399 (gelap)
        const colorOnline      = dark ? '#34D399' : '#16A34A';
        const colorOnlineHover = dark ? '#10B981' : '#15803D';

        // Offline: merah #DC2626 (terang) / #F87171 (gelap)
        const colorOffline     = dark ? '#F87171' : '#DC2626';
        const colorOfflineHover= dark ? '#EF4444' : '#B91C1C';

        // 1. Chart Status Update Konten
        // [Sudah Update = Caramel, Belum Update = Cream, Tidak Diketahui = Terracotta]
        const statusCanvas = document.getElementById('statusChart');
        if (statusCanvas) {
            const labels = ['Sudah Update', 'Belum Update', 'Tidak Diketahui'];
            const dataVals = [sudahUpdate, belumUpdate, tidakDiketahui];
            const statusBarColors = [colorSudah, colorBelum, colorTidak];
            const statusHoverColors = [colorSudahHover, colorBelumHover, colorTidakHover];

            if (chartStatusInstance) chartStatusInstance.destroy();

            chartStatusInstance = new Chart(statusCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataVals,
                        backgroundColor: statusBarColors,
                        hoverBackgroundColor: statusHoverColors,
                        borderRadius: 999,
                        borderSkipped: false,
                        barThickness: 26,
                        maxBarThickness: 34
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colorTooltipBg,
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            cornerRadius: 8,
                            padding: 10,
                            displayColors: false,
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif" }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: colorMuted,
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colorBorder,
                                borderDash: [4, 4],
                                drawBorder: false
                            },
                            border: { display: false },
                            ticks: {
                                color: colorMuted,
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                                precision: 0
                            }
                        }
                    }
                }
            });
        }

        // 2. Chart Status Koneksi STB
        // [Online = Hijau, Offline = Merah]
        const koneksiCanvas = document.getElementById('koneksiChart');
        if (koneksiCanvas) {
            const labels = ['Online', 'Offline'];
            const dataVals = [signageOnline, signageOffline];
            const koneksiBarColors = [colorOnline, colorOffline];
            const koneksiHoverColors = [colorOnlineHover, colorOfflineHover];

            if (chartKoneksiInstance) chartKoneksiInstance.destroy();

            chartKoneksiInstance = new Chart(koneksiCanvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: dataVals,
                        backgroundColor: koneksiBarColors,
                        hoverBackgroundColor: koneksiHoverColors,
                        borderRadius: 999,
                        borderSkipped: false,
                        barThickness: 28,
                        maxBarThickness: 36
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: colorTooltipBg,
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            cornerRadius: 8,
                            padding: 10,
                            displayColors: false,
                            titleFont: { family: "'Plus Jakarta Sans', sans-serif", weight: '700' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif" }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            border: { display: false },
                            ticks: {
                                color: colorMuted,
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11, weight: '600' }
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: colorBorder,
                                borderDash: [4, 4],
                                drawBorder: false
                            },
                            border: { display: false },
                            ticks: {
                                color: colorMuted,
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                                precision: 0
                            }
                        }
                    }
                }
            });
        }
    }

    // Eksekusi render pertama
    function initCharts() {
        renderSegmentedRing();
        renderBarCharts();
    }
    initCharts();

    // Reaktif saat tema bertukar (via event khusus atau MutationObserver)
    window.addEventListener('vortexThemeChanged', function() {
        renderSegmentedRing();
        renderBarCharts();
    });

    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(m) {
            if (m.attributeName === 'data-theme') {
                renderSegmentedRing();
                renderBarCharts();
            }
        });
    });
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-theme'] });
})();
</script>
@endpush
