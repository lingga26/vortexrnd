<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kelola Client') | ADMIN PORTAL</title>
    
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') . '?v=' . filemtime(public_path('images/logo.png')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Instant Theme Loader -->
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
        :root {
            /* Surface */
            --bg-page: #F4F6FB;          /* Clean SaaS background */
            --bg-card: #FFFFFF;          /* Putih bersih */
            --bg-topbar: #FFFFFF;
            --border: #DBE2EE;           /* Border tabel & garis */

            /* Sidebar & Navigation */
            --sidebar-bg: #111C30;
            --sidebar-icon: #C7D2E4;
            --sidebar-active-bg: #2563EB;
            --sidebar-active-fg: #FFFFFF;

            /* Brand / Accent */
            --accent: #2563EB;          /* Biru royal */
            --accent-hover: #1D4ED8;
            --accent-soft: rgba(37, 99, 235, 0.12);
            --btn-primary-fg: #FFFFFF;
            --purple: #4F46E5;
            --ink: #16233A;             /* Teks utama */
            --muted: #64748B;           /* Teks redup / sekunder */
            --track: #E2E8F0;           /* Pill / track */

            /* Stat Card Gradients */
            --stat-lavender: linear-gradient(135deg, #EFF6FF, #DBEAFE);
            --stat-blue:     linear-gradient(135deg, #F0FDF4, #DCFCE7);
            --stat-pink:     linear-gradient(135deg, #FAF5FF, #F3E8FF);
            --stat-red:      linear-gradient(135deg, #FEF2F2, #FEE2E2);
            --stat-green:    linear-gradient(135deg, #ECFDF5, #D1FAE5);
            --stat-amber:    linear-gradient(135deg, #FFFBEB, #FEF3C7);

            /* Badges */
            --badge-up-bg: rgba(22, 163, 74, 0.12);   --badge-up-fg: #16A34A;
            --badge-down-bg: rgba(220, 38, 38, 0.12); --badge-down-fg: #DC2626;
            --badge-warn-bg: rgba(217, 119, 6, 0.14); --badge-warn-fg: #D97706;
            --badge-neutral-bg: rgba(100, 116, 139, 0.12); --badge-neutral-fg: #64748B;

            --radius-card: 20px;
            --radius-pill: 999px;
            --shadow-card: 0 4px 16px rgba(17, 28, 48, 0.05), 0 1px 3px rgba(17, 28, 48, 0.03);

            --bg-body: var(--bg-page);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --bg-table-th: var(--bg-card);
            --bg-input: var(--bg-card);
            --color-primary: #2563EB;
            --color-accent: #2563EB;
            --color-success: #16A34A;
            --color-warning: #D97706;
            --color-danger: #DC2626;
        }

        [data-theme="dark"] {
            /* Surface */
            --bg-page: #0F1D33;          /* Midnight Broadcast */
            --bg-card: #16283F;          /* Midnight card */
            --bg-topbar: #0A1628;
            --border: #27405F;           /* Border tabel & garis gelap */

            /* Sidebar & Navigation */
            --sidebar-bg: #0A1628;
            --sidebar-active-bg: #164E63;

            /* Brand / Accent */
            --accent: #22D3EE;          /* Cyan */
            --accent-hover: #38E1F5;
            --accent-soft: rgba(34, 211, 238, 0.15);
            --btn-primary-fg: #06202B;
            --ink: #E6EEF7;             /* Teks utama gelap */
            --muted: #8CA3BF;           /* Teks redup gelap */
            --track: #101F35;           /* Pill / track gelap */

            /* Badges Dark */
            --badge-up-bg: rgba(52, 211, 153, 0.16);   --badge-up-fg: #34D399;
            --badge-down-bg: rgba(248, 113, 113, 0.18); --badge-down-fg: #F87171;
            --badge-warn-bg: rgba(251, 191, 36, 0.18);  --badge-warn-fg: #FBBF24;
            --badge-neutral-bg: rgba(140, 163, 191, 0.18); --badge-neutral-fg: #8CA3BF;

            --shadow-card: none;

            --bg-body: var(--bg-page);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --bg-table-th: #16283F;
            --bg-input: #0F1D33;
            --color-primary: #22D3EE;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body { 
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            font-size: 14px; 
            background: var(--bg-page); 
            display: flex; 
            flex-direction: column; 
            min-height: 100vh; 
            color: var(--ink); 
            padding: 16px;
            transition: background-color 0.2s ease, color 0.2s ease;
            -webkit-font-smoothing: antialiased;
        }

        .card, .pastel-card, .table-responsive, .vortex-card-index {
            background-color: var(--bg-card) !important;
            border: 1px solid var(--border) !important;
            color: var(--ink) !important;
            border-radius: var(--radius-card);
            box-shadow: var(--shadow-card) !important;
        }

        .card-header, .card-footer, .pastel-card-header {
            background-color: transparent !important;
            border-bottom: 1px solid var(--border) !important;
            color: var(--ink) !important;
            font-weight: 700;
        }

        .table, .table-pastel {
            background-color: transparent !important;
            color: var(--ink) !important;
            width: 100% !important;
            border-collapse: collapse !important;
        }

        .table th, .table-pastel th {
            background-color: var(--bg-card) !important;
            color: var(--muted) !important;
            font-size: 11px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
            border-bottom: 1px solid var(--border) !important;
            padding: 14px 16px !important;
        }

        .table td, .table-pastel td {
            background-color: transparent !important;
            border-bottom: 1px solid var(--border) !important;
            color: var(--ink) !important;
            padding: 14px 16px !important;
            font-size: 13.5px !important;
        }

        .table tr:hover td, .table-pastel tr:hover td {
            background-color: rgba(37, 99, 235, 0.04) !important;
        }
        [data-theme="dark"] .table tr:hover td, [data-theme="dark"] .table-pastel tr:hover td {
            background-color: rgba(34, 211, 238, 0.05) !important;
        }

        /* Topbar Kartu Melayang */
        .topbar {
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
        }
        .topbar .brand { font-weight: 800; font-size: 17px; letter-spacing: 0.5px; color: #16233A; }
        [data-theme="dark"] .topbar .brand { color: var(--accent); } 
        
        .topbar .nav-admin { display: flex; align-items: center; gap: 8px; margin-left: 24px; }
        .topbar .nav-admin a {
            display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: var(--radius-pill);
            color: var(--ink); text-decoration: none; font-weight: 700; font-size: 12.5px;
            background: var(--track); transition: all 0.15s ease;
        }
        .topbar .nav-admin a:hover { opacity: 0.9; transform: translateY(-1px); }
        .topbar .nav-admin a.active { background: var(--accent); color: var(--btn-primary-fg, #FFFFFF); }
        .topbar .nav-admin a.logout { color: #FFFFFF; background: #DC2626; }
        .topbar .nav-admin a.logout:hover { background: #B91C1C; }

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
        }
        .topbar-btn-circle:hover {
            background: var(--border);
            transform: translateY(-1px);
            color: var(--accent);
        }

        .main-content { flex: 1; display: flex; flex-direction: column; }
        .page-header { font-size: 22px; font-weight: 800; color: var(--ink); letter-spacing: -0.3px; margin-bottom: 20px; }
        .content { flex: 1; }

        .btn {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 18px; border-radius: var(--radius-pill); font-size: 13px; font-weight: 700;
            cursor: pointer; border: 1px solid transparent; text-decoration: none; transition: all .18s;
        }
        .btn:hover { transform: translateY(-1px); opacity: .95; }
        .btn-primary, .btn-success { background: var(--accent) !important; color: var(--btn-primary-fg, #FFFFFF) !important; border-color: var(--accent); }
        .btn-primary:hover, .btn-success:hover { background: var(--accent-hover) !important; border-color: var(--accent-hover); }
        .btn-warning { background: #f59e0b; color: #fff; border-color: #f59e0b; }
        .btn-danger  { background: #ef4444; color: #fff; border-color: #ef4444; }

        .form-control, .search-input, select, textarea {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border) !important; border-radius: 12px;
            font-size: 13.5px; color: var(--ink) !important; background: var(--bg-card) !important;
            font-family: inherit;
        }
        .form-control:focus, .search-input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--accent) !important; box-shadow: 0 0 0 3px var(--accent-soft) !important;
        }

        .alert { padding: 12px 18px; border-radius: 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid transparent; }
        .alert-success { background: var(--badge-up-bg); color: var(--badge-up-fg); }
        .alert-danger  { background: var(--badge-down-bg); color: var(--badge-down-fg); }

        [data-theme="dark"] {
            color-scheme: dark;
        }
        [data-theme="dark"] select option { background: #16283F; color: #E6EEF7; }
        [data-theme="dark"] ::placeholder { color: var(--muted); opacity: .7; }
        [data-theme="dark"] [style*="color: #2c3e50"], [data-theme="dark"] [style*="color:#2c3e50"] { color: var(--ink) !important; }
        [data-theme="dark"] [style*="background:#f8f9fa"], [data-theme="dark"] [style*="background: #f8f9fa"] { background: #16283F !important; color: var(--ink) !important; }

        .pagination { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--bg-card); color: var(--ink); text-decoration: none; font-weight: 700; font-size: 13px;
        }
        .pagination a:hover { background: var(--track); }
        .pagination span:not(.current) { opacity: .5; }
        .pagination .current { background: var(--accent); border-color: var(--accent); color: var(--btn-primary-fg, #FFFFFF); opacity: 1; }
    </style>
</head>
<body>

<div class="topbar">
    <div style="display:flex; align-items:center;">
        <span class="brand" style="display:inline-flex; align-items:center; gap:8px;">
            <img src="{{ asset('images/logo.png') }}" alt="VortexSign" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border: 1.5px solid var(--accent);">
            <span>ADMINPORTAL</span>
        </span>
        <nav class="nav-admin">
            <a href="{{ route('clients.index') }}" class="{{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Kelola Client
            </a>
            <a href="{{ route('perawatan.index') }}" class="{{ request()->routeIs('perawatan.*') ? 'active' : '' }}">
                <i class="fas fa-broom"></i> Perawatan Media
            </a>
        </nav>
    </div>

    <div style="display: flex; align-items: center; gap: 14px;">
        <button id="theme-toggle" type="button" class="topbar-btn-circle" title="Toggle Tema">
            <i id="theme-toggle-icon" class="fas fa-moon"></i>
        </button>
        <span style="font-size: 13px; color: var(--muted); font-weight: 700;">
            <i class="fas fa-user-shield" style="color: var(--accent);"></i> {{ Auth::user()->name ?? 'Super Admin' }}
        </span>
        <div class="nav-admin" style="margin-left:0;">
            <a href="#" class="logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit()">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>
</div>

<div class="main-content">
    <div class="page-header">@yield('page-title', 'Kelola Client')</div>
    <div class="content">
        @yield('content')
    </div>
</div>

<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none;">@csrf</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');
    
    function syncThemeIcon() {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        if (themeToggleIcon) {
            if (currentTheme === 'dark') {
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
        themeToggleBtn.addEventListener('click', () => {
            let theme = document.documentElement.getAttribute('data-theme') || 'light';
            let next = (theme === 'dark') ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', next);
            localStorage.setItem('theme', next);
            syncThemeIcon();
        });
    }
});
</script>
@stack('scripts')
</body>
</html>