<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Kelola Client') | ADMIN PORTAL</title>
    
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
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
            --bg-page: #FAF6F0;          /* cream / warm soft ivory */
            --bg-card: #FFFFFF;          /* putih bersih */
            --border: #ECE4D8;           /* cream border */

            --sidebar-bg: #2D1810;       /* coklat espresso tua */
            --sidebar-icon: #FFFFFF;
            --sidebar-active-bg: #DFCEBC;/* cream biscuit */
            --sidebar-active-fg: #2D1810;

            --accent: #A06841;          /* coklat karamel / toffee */
            --accent-hover: #86532F;
            --accent-soft: #DFCEBC;
            --purple: #6B4423;
            --ink: #26160F;             /* coklat pekat */
            --muted: #827063;           /* taupe / kopi susu */
            --track: #EFE7DD;           /* cream oatmeal */

            --stat-lavender: linear-gradient(135deg, #FBF8F4, #F4ECE1);
            --stat-blue:     linear-gradient(135deg, #F7F0E6, #EFE3D3);
            --stat-pink:     linear-gradient(135deg, #F8E4F8, #FBEFFB);
            --stat-red:      linear-gradient(135deg, #F4DDCF, #E8C8B5);
            --stat-green:    linear-gradient(135deg, #F3E7D8, #E9DAC6);
            --stat-amber:    linear-gradient(135deg, #F6E5D3, #EACFB8);

            --badge-up-bg: #EAE2D5;   --badge-up-fg: #6E492B;
            --badge-down-bg: #F7E1D8; --badge-down-fg: #B85D43;

            --radius-card: 20px;
            --radius-pill: 999px;
            --shadow-card: 0 4px 16px rgba(45, 24, 16, 0.04), 0 1px 3px rgba(45, 24, 16, 0.02);

            --bg-body: var(--bg-page);
            --bg-topbar: var(--bg-card);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --bg-table-th: var(--track);
            --bg-input: var(--bg-card);
            --color-primary: #2D1810;
            --color-accent: #A06841;
            --color-success: #6E492B;
            --color-warning: #B5895D;
            --color-danger: #BA5D42;
        }

        [data-theme="dark"] {
            --bg-page: #150D09;
            --bg-card: #1F140E;
            --border: #2D1D15;

            --sidebar-bg: #1B0F0A;
            --sidebar-active-bg: #DFCEBC;

            --accent: #D4A373;
            --accent-soft: #38241A;
            --ink: #FBF6F0;
            --muted: #A89587;
            --track: #281A12;

            --badge-up-bg: rgba(212, 163, 115, 0.15);  --badge-up-fg: #E2B689;
            --badge-down-bg: rgba(186, 93, 66, 0.22); --badge-down-fg: #E58267;

            --shadow-card: none;

            --bg-body: var(--bg-page);
            --bg-topbar: var(--bg-card);
            --border-main: var(--border);
            --border-sidebar: rgba(255, 255, 255, 0.08);
            --border-table: var(--border);
            --text-main: var(--ink);
            --text-muted: var(--muted);
            --bg-table-th: #241610;
            --bg-input: #261711;
            --color-primary: #D4A373;
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
            background-color: rgba(160, 104, 65, 0.05) !important;
        }
        [data-theme="dark"] .table tr:hover td, [data-theme="dark"] .table-pastel tr:hover td {
            background-color: rgba(255, 255, 255, 0.03) !important;
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
        .topbar .brand { font-weight: 800; font-size: 17px; letter-spacing: 0.5px; color: #2D1810; }
        [data-theme="dark"] .topbar .brand { color: var(--accent); } 
        
        .topbar .nav-admin { display: flex; align-items: center; gap: 8px; margin-left: 24px; }
        .topbar .nav-admin a {
            display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: var(--radius-pill);
            color: var(--ink); text-decoration: none; font-weight: 700; font-size: 12.5px;
            background: var(--track); transition: all 0.15s ease;
        }
        .topbar .nav-admin a:hover { opacity: 0.9; transform: translateY(-1px); }
        .topbar .nav-admin a.active { background: var(--accent); color: #fff; }
        .topbar .nav-admin a.logout { color: #FFFFFF; background: #BA5D42; }

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
        .btn-primary, .btn-success { background: var(--accent) !important; color: #fff; border-color: var(--accent); }
        .btn-warning { background: #f59e0b; color: #fff; border-color: #f59e0b; }
        .btn-danger  { background: #ef4444; color: #fff; border-color: #ef4444; }

        .form-control, .search-input, select, textarea {
            width: 100%; padding: 10px 14px; border: 1px solid var(--border) !important; border-radius: 12px;
            font-size: 13.5px; color: var(--ink) !important; background: var(--bg-card) !important;
            font-family: inherit;
        }
        .form-control:focus, .search-input:focus, select:focus, textarea:focus {
            outline: none; border-color: var(--accent) !important; box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.18) !important;
        }

        .alert { padding: 12px 18px; border-radius: 14px; font-size: 13px; font-weight: 600; margin-bottom: 20px; border: 1px solid transparent; }
        .alert-success { background: var(--badge-up-bg); color: var(--badge-up-fg); }
        .alert-danger  { background: var(--badge-down-bg); color: var(--badge-down-fg); }

        [data-theme="dark"] {
            color-scheme: dark;
        }
        [data-theme="dark"] select option { background: #151516; color: #FFFFFF; }
        [data-theme="dark"] ::placeholder { color: var(--muted); opacity: .7; }
        [data-theme="dark"] [style*="color: #2c3e50"], [data-theme="dark"] [style*="color:#2c3e50"] { color: var(--ink) !important; }
        [data-theme="dark"] [style*="background:#f8f9fa"], [data-theme="dark"] [style*="background: #f8f9fa"] { background: #1c1c1e !important; color: var(--ink) !important; }

        .pagination { display: flex; gap: 6px; flex-wrap: wrap; align-items: center; }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: 10px; border: 1px solid var(--border);
            background: var(--bg-card); color: var(--ink); text-decoration: none; font-weight: 700; font-size: 13px;
        }
        .pagination a:hover { background: var(--track); }
        .pagination span:not(.current) { opacity: .5; }
        .pagination .current { background: var(--accent); border-color: var(--accent); color: #fff; opacity: 1; }
    </style>
</head>
<body>

<div class="topbar">
    <div style="display:flex; align-items:center;">
        <span class="brand" style="display:inline-flex; align-items:center; gap:8px;">
            <img src="{{ asset('images/logo.png') }}" alt="VortexSign" style="width:26px; height:26px; border-radius:50%; object-fit:cover; border: 1.5px solid rgba(160, 104, 65, 0.35);">
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
