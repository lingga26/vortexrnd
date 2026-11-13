<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Ganti Password | CMS Digital Signage</title>
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
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
            --accent: #A06841;           /* coklat karamel */
            --accent-hover: #86532F;
            --ink: #26160F;              /* coklat tua pekat */
            --muted: #827063;            /* taupe / kopi susu */
            --track: #EFE7DD;            /* cream oatmeal */
            --badge-up-bg: #EAE2D5;
            --badge-up-fg: #6E492B;
            --badge-down-bg: #F7E1D8;
            --badge-down-fg: #B85D43;
            --radius-card: 24px;
            --radius-pill: 999px;
            --shadow-card: 0 20px 45px rgba(45, 24, 16, 0.08), 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        [data-theme="dark"] {
            --bg-page: #150D09;
            --bg-card: #1F140E;
            --border: #2D1D15;
            --ink: #FBF6F0;
            --muted: #A89587;
            --track: #281A12;
            --accent: #D4A373;
            --accent-hover: #E2B689;
            --badge-up-bg: rgba(212, 163, 115, 0.15);
            --badge-up-fg: #E2B689;
            --badge-down-bg: rgba(186, 93, 66, 0.22);
            --badge-down-fg: #E58267;
            --shadow-card: 0 20px 45px rgba(0, 0, 0, 0.45);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; }
        
        body {
            background-color: var(--bg-page);
            color: var(--ink);
            min-height: 100vh;
            display: flex; align-items: center; justify-content: center; padding: 20px;
            overflow: hidden; position: relative;
            transition: background-color 0.2s ease, color 0.2s ease;
            -webkit-font-smoothing: antialiased;
        }

        body::before, body::after {
            content: ""; position: absolute; border-radius: 50%; z-index: 1; filter: blur(120px); pointer-events: none;
        }
        body::before {
            width: 450px; height: 450px; background: rgba(186, 143, 110, 0.16);
            top: -100px; right: -80px;
        }
        body::after {
            width: 420px; height: 420px; background: rgba(45, 24, 16, 0.14);
            bottom: -100px; left: -80px;
        }

        .box {
            position: relative; z-index: 2;
            background: var(--bg-card);
            width: 100%; max-width: 420px;
            border-radius: var(--radius-card);
            overflow: hidden;
            box-shadow: var(--shadow-card);
            border: 1px solid var(--border);
            animation: slideUp 0.4s ease-out;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .login-accent-bar {
            height: 5px; width: 100%;
            background: linear-gradient(90deg, #2D1810 0%, #A06841 50%, #DFCEBC 100%);
        }

        .head {
            text-align: center; padding: 32px 24px 8px;
        }
        .head-logo-circle {
            width: 58px; height: 58px; border-radius: 50%;
            overflow: hidden; margin: 0 auto 14px;
            border: 2.5px solid #BA8F6E;
            box-shadow: 0 4px 16px rgba(45, 24, 16, 0.25);
            background: #2D1810; display: flex; align-items: center; justify-content: center;
        }
        .head-logo-circle img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .head h1 { font-size: 24px; font-weight: 800; color: #2D1810; letter-spacing: -0.5px; }
        [data-theme="dark"] .head h1 { color: #FBF6F0; }
        .head h1 span { color: var(--accent); }
        .head p { font-size: 11.5px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        .body { padding: 22px 32px 32px; }
        
        .notice {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px; font-weight: 600;
            background: rgba(181, 137, 93, 0.15); color: #7a4e1d; border-left: 4px solid #B5895D;
            display: flex; align-items: center; gap: 8px;
        }
        [data-theme="dark"] .notice { background: rgba(181, 137, 93, 0.25); color: #E2B689; }

        .alert-danger {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px; font-weight: 600;
            background: var(--badge-down-bg); color: var(--badge-down-fg); border-left: 4px solid #B85D43;
            display: flex; align-items: center; gap: 8px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 11.5px; font-weight: 800; color: var(--muted);
            margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.8px;
        }
        .form-control {
            width: 100%; padding: 13px 16px; border: 1.5px solid var(--border); border-radius: 14px;
            font-size: 14px; font-weight: 600; background: var(--bg-card); color: var(--ink); outline: none;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.22);
        }

        .btn {
            width: 100%; padding: 14px; background: #2D1810; color: #fff; border: none; border-radius: var(--radius-pill);
            font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(45, 24, 16, 0.35);
            transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn:hover {
            background: var(--accent);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(160, 104, 65, 0.45);
        }
        .out { text-align: center; margin-top: 18px; }
        .out button {
            background: none; border: none; color: var(--muted); font-size: 13px; font-weight: 700; cursor: pointer;
            transition: color 0.15s;
        }
        .out button:hover { color: #B85D43; }
    </style>
</head>
<body>
<div class="box">
    <div class="login-accent-bar"></div>

    <div class="head">
        <div class="head-logo-circle">
            <img src="{{ asset('images/logo.png') }}" alt="VortexSign Logo">
        </div>
        <h1>VORTEX<span>SIGN</span></h1>
        <p>Ganti Password Akun</p>
    </div>

    <div class="body">
        @if(auth()->user()->must_change_password)
            <div class="notice"><i class="fas fa-key"></i> Password Anda baru direset admin. Buat password baru untuk melanjutkan.</div>
        @endif

        @if($errors->any())
            <div class="alert-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('password.ganti.simpan') }}">
            @csrf
            <div class="form-group">
                <label for="password">Password baru (min. 8 karakter)</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru" autocomplete="new-password" required autofocus>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Ulangi password baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" placeholder="Ulangi password baru" autocomplete="new-password" required>
            </div>
            <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Password</button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="out">
            @csrf
            <button type="submit"><i class="fas fa-sign-out-alt"></i> Keluar / Batal</button>
        </form>
    </div>
</div>
</body>
</html>
