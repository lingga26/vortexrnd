<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CMS Digital Signage') | ADMINPAGE</title>
    
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome & Chart.js -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Flatpickr (Timepicker & Datepicker) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Instant Theme Loader (Mencegah Layar Berkedip / FOUC) -->
    <script>
        (function() {
            try {
                var saved = localStorage.getItem('theme');
                var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                var theme = saved ? saved : (prefersDark ? 'dark' : 'light');
                document.documentElement.setAttribute('data-theme', theme);
            } catch(e) {}
        })();
    </script>

    <style>
        /* ==========================================================================
           🎨 1. DESIGN TOKENS (LIGHT & DARK MODE - COKLAT, PUTIH, CREAM)
           ========================================================================== */
        :root {
            /* Surface */
            --bg-page: #FAF6F0;          /* cream / warm soft ivory */
            --bg-card: #FFFFFF;          /* putih bersih */
            --border: #ECE4D8;           /* cream border lembut */

            /* Sidebar */
            --sidebar-bg: #2D1810;       /* coklat espresso tua pekat */
            --sidebar-icon: #FFFFFF;
            --sidebar-active-bg: #DFCEBC;/* cream biscuit hangat */
            --sidebar-active-fg: #2D1810;/* teks coklat pekat pada tombol aktif */
            --sidebar-logout-bg: #BA5D42;/* terracotta / rust pill button */
            --sidebar-logout-fg: #FFFFFF;

            /* Brand / accent */
            --accent: #A06841;          /* coklat karamel / toffee hangat */
            --accent-hover: #86532F;
            --accent-soft: #DFCEBC;     /* cream biscuit */
            --purple: #6B4423;          /* coklat tua pendamping */
            --ink: #26160F;             /* teks utama: coklat pekat */
            --muted: #827063;           /* teks sekunder: taupe / kopi susu */
            --track: #EFE7DD;           /* pill / track: cream oatmeal */

            /* Kartu statistik (gradient cream, almond, sand, peach, terracotta) */
            --stat-lavender: linear-gradient(135deg, #FBF8F4, #F4ECE1);
            --stat-blue:     linear-gradient(135deg, #F7F0E6, #EFE3D3);
            --stat-green:    linear-gradient(135deg, #F3E7D8, #E9DAC6);
            --stat-amber:    linear-gradient(135deg, #F6E5D3, #EACFB8);
            --stat-red:      linear-gradient(135deg, #F4DDCF, #E8C8B5);

            /* Tombol panah stat card */
            --stat-arrow-bg: #BA8F6E;
            --stat-arrow-red-bg: #C2735B;

            /* Badge persentase */
            --badge-up-bg: #EAE2D5;   --badge-up-fg: #6E492B;
            --badge-down-bg: #F7E1D8; --badge-down-fg: #B85D43;

            --radius-card: 20px;
            --radius-pill: 999px;
            --shadow-card: 0 4px 16px rgba(45, 24, 16, 0.04), 0 1px 3px rgba(45, 24, 16, 0.02);

            /* Alias kompatibilitas view lama */
            --bg-body: var(--bg-page);
            --bg-topbar: var(--bg-card);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --text-section: rgba(255, 255, 255, 0.42);
            --bg-table-th: var(--bg-card);
            --bg-input: var(--bg-card);
            --color-primary: #2D1810;
            --color-accent: #A06841;
            --color-success: #6E492B;
            --color-warning: #B5895D;
            --color-info: #827063;
            --color-danger: #BA5D42;
        }

        [data-theme="dark"] {
            --bg-page: #150D09;          /* dark roast coffee */
            --bg-card: #1F140E;          /* dark espresso card */
            --border: #2D1D15;

            --sidebar-bg: #1B0F0A;
            --sidebar-active-bg: #DFCEBC;
            --sidebar-active-fg: #1B0F0A;
            --sidebar-logout-bg: #BA5D42;
            --sidebar-logout-fg: #FFFFFF;

            --accent: #D4A373;
            --accent-hover: #E2B689;
            --accent-soft: #38241A;
            --ink: #FBF6F0;
            --muted: #A89587;
            --track: #281A12;

            /* Kartu statistik tema gelap */
            --stat-lavender: linear-gradient(135deg, #241610, #1F130E);
            --stat-blue:  #1F140E;
            --stat-pink:  #1F140E;
            --stat-red:   #1F140E;
            --stat-green: #1F140E;
            --stat-amber: #1F140E;

            --stat-arrow-bg: #A06841;
            --stat-arrow-red-bg: #BA5D42;

            --badge-up-bg: rgba(212, 163, 115, 0.15);  --badge-up-fg: #E2B689;
            --badge-down-bg: rgba(186, 93, 66, 0.22); --badge-down-fg: #E58267;

            --shadow-card: none;

            /* Alias kompatibilitas tema gelap */
            --bg-body: var(--bg-page);
            --bg-topbar: var(--bg-card);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --text-section: rgba(255, 255, 255, 0.42);
            --bg-table-th: var(--bg-card);
            --bg-input: #261711;
            --color-primary: #D4A373;
        }

        /* ==========================================================================
           🧱 2. LAYOUT DASAR & TIPOGRAFI
           ========================================================================== */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px;
            background: var(--bg-page);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            transition: background-color 0.2s ease, color 0.2s ease;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            max-width: 100vw;
        }

        /* App Wrapper */
        .app-layout {
            display: flex;
            width: 100%;
            max-width: 100vw;
            min-height: 100vh;
            padding: 16px;
            gap: 16px;
            position: relative;
            overflow-x: hidden;
            box-sizing: border-box;
        }

        /* ==========================================================================
           ☕ 3. FLOATING SIDEBAR (COKLAT GELAP #2D1810)
           ========================================================================== */
        .sidebar {
            width: 250px;
            min-width: 250px;
            background: var(--sidebar-bg);
            color: #FFFFFF;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 16px;
            left: 16px;
            bottom: 16px;
            border-radius: 24px;
            z-index: 1000;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Header Logo Sidebar */
        .sidebar-brand-box {
            padding: 24px 20px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-logo-circle {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #2D1810;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
            overflow: hidden;
            border: 2px solid rgba(223, 206, 188, 0.3);
        }
        .sidebar-logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .sidebar-brand-text {
            display: flex;
            flex-direction: column;
        }
        .sidebar-brand-text .title {
            font-size: 15px;
            font-weight: 800;
            color: #FFFFFF;
            letter-spacing: 0.5px;
            line-height: 1.2;
        }
        .sidebar-brand-text .sub {
            font-size: 11px;
            color: rgba(255, 255, 255, 0.55);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        /* Profil Singkat di Sidebar */
        .sidebar-user-box {
            margin: 0 14px 12px;
            padding: 10px 14px;
            background: rgba(255, 255, 255, 0.06);
            border-radius: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .sidebar-user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: rgba(223, 206, 188, 0.2);
            color: #DFCEBC;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            flex-shrink: 0;
            border: 1px solid rgba(223, 206, 188, 0.3);
        }
        .sidebar-user-meta {
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .sidebar-user-meta strong {
            color: #FFFFFF;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .sidebar-user-meta .role-tag {
            font-size: 10px;
            color: #DFCEBC;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Navigasi Menu */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            padding: 4px 0 16px;
        }
        /* Custom sleek scrollbar */
        .sidebar-nav::-webkit-scrollbar { width: 4px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.15); border-radius: 4px; }

        .sidebar-nav .nav-section {
            padding: 16px 20px 6px;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.42);
            font-weight: 800;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            margin: 4px 14px;
            color: rgba(255, 255, 255, 0.82);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 12px;
            transition: all 0.18s ease;
        }
        .sidebar-nav a i {
            width: 20px;
            font-size: 15px;
            text-align: center;
            flex-shrink: 0;
            transition: transform 0.18s ease;
        }
        .sidebar-nav a:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
            color: #FFFFFF;
            transform: translateX(2px);
        }
        /* Item Aktif: Cream / Biscuit #DFCEBC dengan teks coklat pekat */
        .sidebar-nav a.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-active-fg) !important;
            font-weight: 800;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
        }
        .sidebar-nav a.active i {
            color: var(--sidebar-active-fg) !important;
        }
        /* Tombol Logout: Terracotta / Rust Pill Button */
        .sidebar-nav a.nav-logout {
            margin: 20px 14px 10px;
            background: var(--sidebar-logout-bg, #BA5D42);
            color: #FFFFFF !important;
            font-weight: 700;
            border-radius: var(--radius-pill);
            padding: 12px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(186, 93, 66, 0.35);
            transition: all 0.2s ease;
        }
        .sidebar-nav a.nav-logout i {
            color: #FFFFFF !important;
        }
        .sidebar-nav a.nav-logout:hover {
            background: #A84E35;
            color: #FFFFFF !important;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(186, 93, 66, 0.45);
        }

        /* ==========================================================================
           📄 4. MAIN CONTENT & FLOATING TOPBAR
           ========================================================================== */
        .main-wrapper {
            margin-left: 266px; /* 250px + 16px */
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            max-width: calc(100% - 266px);
            width: calc(100% - 266px);
            box-sizing: border-box;
        }

        /* Topbar Melayang (Rounded Pill Card) */
        .topbar-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card);
            height: 64px;
            padding: 0 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .mobile-menu-btn {
            display: none;
            background: transparent;
            border: none;
            font-size: 18px;
            color: var(--ink);
            cursor: pointer;
            padding: 6px;
        }

        .brand-heading {
            font-size: 17px;
            font-weight: 800;
            letter-spacing: 0.5px;
            color: #2D1810;
        }
        [data-theme="dark"] .brand-heading {
            color: var(--accent);
        }

        .realtime-clock-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            background: var(--track);
            color: var(--ink);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Tombol Bulat Berlatar --track */
        .topbar-btn-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: var(--track);
            border: 1px solid var(--border);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.18s ease;
            text-decoration: none;
        }
        .topbar-btn-circle:hover {
            background: var(--border);
            transform: translateY(-1px);
            color: var(--accent);
        }

        .topbar-cms-badge {
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            background: var(--track);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .topbar-user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #2D1810;
            color: #FFFFFF;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 2px 8px rgba(45, 24, 16, 0.25);
        }

        /* Konten Halaman */
        .content-body {
            flex: 1;
            min-width: 0;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        /* ==========================================================================
           📦 5. KOMPONEN GLOBAL (CARD, TABLE, BUTTON, FORMS, BADGE)
           ========================================================================== */
        .card, .pastel-card, .vortex-card-index, .table-responsive {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-card) !important;
            box-shadow: var(--shadow-card) !important;
            color: var(--ink) !important;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .card-header, .card-footer, .pastel-card-header {
            background: transparent !important;
            border-bottom: 1px solid var(--border) !important;
            color: var(--ink) !important;
            font-weight: 700;
        }

        /* Tabel Melebar Bersih */
        .table, .table-pastel, .table-vortex {
            width: 100% !important;
            border-collapse: collapse !important;
            background: transparent !important;
            color: var(--ink) !important;
        }

        .table th, .table-pastel th, .table-vortex th {
            background-color: var(--bg-card) !important;
            color: var(--muted) !important;
            font-weight: 700 !important;
            font-size: 11px !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            border-bottom: 1px solid var(--border) !important;
            padding: 14px 16px !important;
            vertical-align: middle !important;
        }

        .table td, .table-pastel td, .table-vortex td {
            background-color: transparent !important;
            border-bottom: 1px solid var(--border) !important;
            color: var(--ink) !important;
            font-size: 13.5px !important;
            padding: 14px 16px !important;
            vertical-align: middle !important;
            transition: background-color 0.15s ease;
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        .table tbody tr:hover td, .table-pastel tbody tr:hover td, .table-vortex tbody tr:hover td {
            background-color: rgba(160, 104, 65, 0.05) !important;
        }
        [data-theme="dark"] .table tbody tr:hover td,
        [data-theme="dark"] .table-pastel tbody tr:hover td,
        [data-theme="dark"] .table-vortex tbody tr:hover td {
            background-color: rgba(255, 255, 255, 0.03) !important;
        }

        /* Tombol */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: var(--radius-pill);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            border: 1px solid transparent;
            text-decoration: none;
            transition: all 0.18s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }
        .btn:hover {
            transform: translateY(-1px);
            opacity: 0.95;
        }
        .btn-primary, .btn-success {
            background: var(--accent) !important;
            color: #FFFFFF !important;
            border-color: var(--accent) !important;
        }
        .btn-primary:hover, .btn-success:hover {
            filter: brightness(1.05);
        }
        .btn-warning {
            background: #f59e0b !important;
            color: #FFFFFF !important;
            border-color: #f59e0b !important;
        }
        .btn-danger {
            background: #ef4444 !important;
            color: #FFFFFF !important;
            border-color: #ef4444 !important;
        }
        .btn-secondary {
            background: var(--track) !important;
            color: var(--ink) !important;
            border-color: var(--border) !important;
        }

        .btn-icon-square {
            width: 34px !important;
            height: 34px !important;
            padding: 0 !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            border-radius: var(--radius-pill) !important;
            font-size: 13px !important;
            transition: all 0.18s ease !important;
            border: none !important;
            text-decoration: none !important;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06) !important;
        }
        .btn-edit-vortex {
            background-color: rgba(245, 158, 11, 0.15) !important;
            color: #d97706 !important;
        }
        .btn-edit-vortex:hover {
            background-color: #f59e0b !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }
        .btn-delete-vortex {
            background-color: rgba(239, 68, 68, 0.14) !important;
            color: #dc2626 !important;
        }
        .btn-delete-vortex:hover {
            background-color: #ef4444 !important;
            color: #ffffff !important;
            transform: translateY(-2px) !important;
        }
        [data-theme="dark"] .btn-edit-vortex {
            background-color: rgba(245, 158, 11, 0.22) !important;
            color: #fbbf24 !important;
        }
        [data-theme="dark"] .btn-delete-vortex {
            background-color: rgba(239, 68, 68, 0.22) !important;
            color: #f87171 !important;
        }

        /* Form Card & Komponen Input / Tombol Form (Create & Edit) */
        .vortex-card-form, .dist-card, .jadwal-card {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-card) !important;
            box-shadow: var(--shadow-card) !important;
            color: var(--ink) !important;
        }
        .vortex-header-form, .dist-header, .jadwal-header {
            border-bottom: 1px solid var(--border) !important;
        }
        .vortex-header-form h5, .dist-header-title, .jadwal-header-title {
            color: var(--ink) !important;
            font-weight: 800 !important;
        }
        .icon-title-box, .dist-icon-box, .jadwal-icon-box {
            background: rgba(160, 104, 65, 0.12) !important;
            color: var(--accent) !important;
            border-radius: 12px !important;
        }
        .vortex-label-locked, .jadwal-label, .dist-label {
            font-size: 11px !important;
            font-weight: 800 !important;
            color: var(--muted) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            margin-bottom: 8px !important;
        }
        .vortex-input-locked, .jadwal-input, .jadwal-textarea, .dist-input, .dist-select, select.vortex-input-locked {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            color: var(--ink) !important;
            font-size: 13.5px !important;
            font-family: inherit !important;
        }
        .vortex-input-locked:focus, .jadwal-input:focus, .jadwal-textarea:focus, .dist-input:focus {
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.18) !important;
            background: var(--bg-card) !important;
        }
        .btn-save-v, .dist-btn-submit, .jadwal-btn-submit {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #FFFFFF !important;
            border-radius: var(--radius-pill) !important;
            font-weight: 700 !important;
            border: none !important;
            box-shadow: 0 4px 12px rgba(160, 104, 65, 0.28) !important;
            cursor: pointer;
        }
        .btn-save-v:hover, .dist-btn-submit:hover, .jadwal-btn-submit:hover {
            filter: brightness(1.05);
            transform: translateY(-1px);
        }
        .btn-cancel-v, .dist-btn-cancel, .jadwal-btn-cancel, .btn-back-v {
            background: var(--track) !important;
            border: 1px solid var(--border) !important;
            color: var(--ink) !important;
            border-radius: var(--radius-pill) !important;
            font-weight: 700 !important;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-cancel-v:hover, .dist-btn-cancel:hover, .jadwal-btn-cancel:hover, .btn-back-v:hover {
            background: var(--border) !important;
            transform: translateY(-1px);
        }

        /* Toolbar Index */
        .toolbar-vortex .btn-primary {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #FFFFFF !important;
            border-radius: var(--radius-pill) !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(160, 104, 65, 0.28) !important;
            border: none !important;
        }
        .toolbar-vortex .btn-secondary {
            background: var(--track) !important;
            border: 1px solid var(--border) !important;
            color: var(--ink) !important;
            border-radius: var(--radius-pill) !important;
        }

        /* Formulir & Input */
        .form-control, .search-input, select, textarea {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid var(--border) !important;
            border-radius: 12px !important;
            font-size: 13.5px;
            color: var(--ink) !important;
            background: var(--bg-card) !important;
            font-family: inherit;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .form-control:focus, .search-input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--accent) !important;
            box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.18) !important;
        }

        /* Notifikasi Alert */
        .alert {
            padding: 12px 18px;
            border-radius: 14px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid transparent;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .alert-success {
            background: var(--badge-up-bg);
            color: var(--badge-up-fg);
            border-color: rgba(160, 104, 65, 0.2);
        }
        .alert-danger {
            background: var(--badge-down-bg);
            color: var(--badge-down-fg);
            border-color: rgba(239, 68, 68, 0.2);
        }

        /* Badges */
        .badge-vortex-soft,
        .badge-vortex-green,
        .badge-vortex-cyan,
        .badge-vortex-content,
        .badge-vortex-bumper,
        .badge-vortex-promo {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
            line-height: 1.2 !important;
            vertical-align: middle;
        }

        .badge-vortex-soft {
            background: var(--track) !important;
            color: var(--muted) !important;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 0.75rem;
        }
        .badge-vortex-green {
            background: var(--badge-up-bg) !important;
            color: var(--badge-up-fg) !important;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 0.75rem;
        }
        .badge-vortex-cyan {
            background: rgba(91, 33, 209, 0.12) !important;
            color: var(--purple) !important;
            padding: 4px 10px;
            border-radius: var(--radius-pill);
            font-weight: 700;
            font-size: 0.75rem;
        }

        /* Flatpickr Styling */
        .flatpickr-calendar {
            background: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-card) !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
            color: var(--ink) !important;
            font-family: inherit !important;
        }
        .flatpickr-time {
            border-top: 1px solid var(--border) !important;
            background: transparent !important;
        }
        .flatpickr-time input,
        .flatpickr-time .flatpickr-time-separator,
        .flatpickr-time .flatpickr-am-pm {
            color: var(--ink) !important;
            font-weight: 700 !important;
        }
        .flatpickr-time input:hover,
        .flatpickr-time input:focus {
            background: var(--track) !important;
        }
        .flatpickr-calendar .numInputWrapper span.arrowUp:after {
            border-bottom-color: var(--ink) !important;
        }
        .flatpickr-calendar .numInputWrapper span.arrowDown:after {
            border-top-color: var(--ink) !important;
        }

        /* Pagination */
        .pagination { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span {
            padding: 6px 12px;
            border-radius: 10px;
            border: 1px solid var(--border);
            background: var(--bg-card);
            color: var(--ink);
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.15s ease;
        }
        .pagination a:hover { background: var(--track); }
        .pagination span:not(.current) { opacity: 0.5; }
        .pagination .current {
            background: var(--accent) !important;
            border-color: var(--accent) !important;
            color: #FFFFFF !important;
            opacity: 1;
        }

        /* ==========================================================================
           🌙 6. ADAPTIF MODE GELAP LENGKAP
           ========================================================================== */
        [data-theme="dark"] {
            color-scheme: dark;
        }
        [data-theme="dark"] select option {
            background: #151516;
            color: #FFFFFF;
        }
        [data-theme="dark"] ::placeholder {
            color: var(--muted);
            opacity: 0.7;
        }
        /* Override teks hardcoded navy */
        [data-theme="dark"] [style*="color: #2c3e50"],
        [data-theme="dark"] [style*="color:#2c3e50"] {
            color: var(--ink) !important;
        }
        [data-theme="dark"] [style*="background:#f8f9fa"],
        [data-theme="dark"] [style*="background: #f8f9fa"] {
            background: #1c1c1e !important;
            color: var(--ink) !important;
        }

        /* ==========================================================================
           📱 7. RESPONSIVE MEDIA QUERIES
           ========================================================================== */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-120%);
                top: 0; left: 0; bottom: 0;
                border-radius: 0 24px 24px 0;
                margin: 0;
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .sidebar-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.5);
                z-index: 999;
            }
            .sidebar-backdrop.active {
                display: block;
            }
            .main-wrapper {
                margin-left: 0;
                max-width: 100%;
                width: 100%;
            }
            .mobile-menu-btn {
                display: inline-flex;
            }
            .realtime-clock-pill {
                display: none;
            }
        }
    </style>
</head>
<body>

<div class="app-layout">
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <!-- 🌲 FLOATING SIDEBAR MELAYANG -->
    <aside class="sidebar" id="appSidebar">
        <!-- Brand Header Logo -->
        <div class="sidebar-brand-box">
            <div class="sidebar-logo-circle">
                <img src="{{ asset('images/logo.png') }}" alt="VortexSign Logo">
            </div>
            <div class="sidebar-brand-text">
                <span class="title">VORTEXSIGN</span>
                <span class="sub">CMS SIGNAGE</span>
            </div>
        </div>

        <!-- Profil Singkat Super Admin -->
        <div class="sidebar-user-box">
            <div class="sidebar-user-avatar">
                <i class="fas fa-user-shield"></i>
            </div>
            <div class="sidebar-user-meta">
                <strong>{{ Auth::user()->name ?? 'Super Admin' }}</strong>
                <span class="role-tag">adminisign</span>
            </div>
        </div>

        <!-- Navigasi Menu Lengkap -->
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> <span>Dashboard</span>
            </a>

            <div class="nav-section">Data Master</div>
            <a href="{{ route('model-signage.index') }}" class="{{ request()->routeIs('model-signage.*') ? 'active' : '' }}">
                <i class="fas fa-tv"></i> <span>Model Signage</span>
            </a>

            <div class="nav-section">Iklan</div>
            <a href="{{ route('toko.index') }}" class="{{ request()->routeIs('toko.*') ? 'active' : '' }}">
                <i class="fas fa-store"></i> <span>Data Toko</span>
            </a>
            <a href="{{ route('signage.index') }}" class="{{ request()->routeIs('signage.*') ? 'active' : '' }}">
                <i class="fas fa-desktop"></i> <span>Digital Signage</span>
            </a>
            <a href="{{ route('file-iklan.index') }}" class="{{ request()->routeIs('file-iklan.*') ? 'active' : '' }}">
                <i class="fas fa-film"></i> <span>File Iklan</span>
            </a>
            <a href="{{ route('jadwal.index') }}" class="{{ request()->routeIs('jadwal.*') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> <span>Jadwal Tayang</span>
            </a>
            <a href="{{ route('distribusi.index') }}" class="{{ request()->routeIs('distribusi.*') ? 'active' : '' }}">
                <i class="fas fa-broadcast-tower"></i> <span>Distribusi Jadwal</span>
            </a>

            <a href="#" class="nav-logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </nav>
    </aside>

    <!-- 📄 MAIN WRAPPER -->
    <div class="main-wrapper">
        <!-- Floating Header Topbar Card -->
        <header class="topbar-card">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" id="sidebarToggle" aria-label="Buka Menu">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="brand-heading" style="display: flex; align-items: center; gap: 10px;">
                    <img src="{{ asset('images/logo.png') }}" alt="VortexSign" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; box-shadow: 0 2px 6px rgba(45, 24, 16, 0.15); border: 1.5px solid rgba(160, 104, 65, 0.35);">
                    <span>ADMINPAGE</span>
                </div>
                <div class="realtime-clock-pill">
                    <i class="far fa-clock" style="color: var(--muted);"></i>
                    <span id="realtime-clock"></span>
                </div>
            </div>

            <div class="topbar-right">
                <!-- Toggle Mode Gelap / Terang -->
                <button id="theme-toggle" type="button" class="topbar-btn-circle" title="Ubah Tema Gelap / Terang" aria-label="Toggle Theme">
                    <i id="theme-toggle-icon" class="fas fa-moon"></i>
                </button>

                <!-- Ikon Bantuan -->
                <div class="topbar-btn-circle" title="Pusat Bantuan">
                    <i class="far fa-question-circle"></i>
                </div>

                <!-- CMS Name Badge -->
                <div class="topbar-cms-badge">
                    <span>Vortexsign CMS</span>
                </div>

                <!-- Avatar Pengguna -->
                <div class="topbar-user-avatar" title="{{ Auth::user()->name ?? 'Super Admin' }}">
                    <i class="fas fa-user-circle"></i>
                </div>
            </div>
        </header>

        <!-- Area Konten Dinamis -->
        <main class="content-body">
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle" style="font-size: 16px;"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle" style="font-size: 16px;"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 20px; border-radius: var(--radius-card); border-left: 4px solid #ef4444;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 16px; margin-top: 2px;"></i>
                    <div>
                        <strong style="display: block; margin-bottom: 4px; font-weight: 800;">Periksa kembali isian formulir:</strong>
                        <ul style="margin: 0; padding-left: 18px; font-size: 13px; font-weight: 600;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            @if(trim($__env->yieldContent('page-title')) && trim($__env->yieldContent('page-title')) !== 'WELCOME TO VORTEXSIGN DASHBOARD')
                <div class="page-title-heading" style="margin-bottom: 20px;">
                    <h2 style="font-size: 20px; font-weight: 800; color: var(--ink); letter-spacing: -0.3px; text-transform: uppercase;">
                        @yield('page-title')
                    </h2>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 🕒 Realtime Clock (Format 24 Jam: Hari, Tanggal Bulan Tahun • 00:00:00 WIB)
    const clockEl = document.getElementById('realtime-clock');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            const dateStr = now.toLocaleDateString('id-ID', options);
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            clockEl.textContent = `${dateStr} • ${hours}:${minutes}:${seconds} WIB`;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }

    // 🌗 Dark Mode Handler & Event Dispatcher
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');
    
    function syncThemeIcon() {
        const current = document.documentElement.getAttribute('data-theme') || 'light';
        if (themeToggleIcon) {
            if (current === 'dark') {
                themeToggleIcon.classList.remove('fa-moon');
                themeToggleIcon.classList.add('fa-sun');
            } else {
                themeToggleIcon.classList.remove('fa-sun');
                themeToggleIcon.classList.add('fa-moon');
            }
        }
    }
    syncThemeIcon();

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', function() {
            let current = document.documentElement.getAttribute('data-theme') || 'light';
            let nextTheme = (current === 'dark') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', nextTheme);
            localStorage.setItem('theme', nextTheme);
            syncThemeIcon();
            // Kirim event agar chart atau komponen reaktif langsung memperbarui warna
            window.dispatchEvent(new CustomEvent('vortexThemeChanged', { detail: { theme: nextTheme } }));
        });
    }

    // 📱 Mobile Sidebar Toggle
    const sidebar = document.getElementById('appSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    if (sidebarToggle && sidebar && sidebarBackdrop) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('open');
            sidebarBackdrop.classList.toggle('active');
        });
        sidebarBackdrop.addEventListener('click', function() {
            sidebar.classList.remove('open');
            sidebarBackdrop.classList.remove('active');
        });
    }
});
</script>

@stack('scripts')
</body>
</html>
