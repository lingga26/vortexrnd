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

    <style>
        :root {
            --bg-page: #0F1D33;          /* Deep tech navy */
            --bg-card: #16283F;          /* Midnight card */
            --border: #27405F;           /* Border garis tech */
            --accent: #22D3EE;           /* Cyan aksen */
            --accent-hover: #38E1F5;
            --accent-blue: #2563EB;      /* Blue primary */
            --accent-blue-hover: #1D4ED8;
            --ink: #E6EEF7;              /* Teks utama */
            --muted: #8CA3BF;            /* Teks redup */
            --track: #0A1628;            /* Darkest navy */
            --radius-card: 24px;
            --radius-pill: 999px;
            --shadow-card: 0 20px 45px rgba(10, 22, 40, 0.5), 0 2px 8px rgba(0, 0, 0, 0.25);
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
            width: 450px; height: 450px; background: rgba(34, 211, 238, 0.12);
            top: -100px; right: -80px;
        }
        body::after {
            width: 420px; height: 420px; background: rgba(37, 99, 235, 0.15);
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
            background: linear-gradient(90deg, #2563EB 0%, #22D3EE 100%);
        }

        .head {
            text-align: center; padding: 32px 24px 8px;
        }
        .head-logo-circle {
            width: 58px; height: 58px; border-radius: 50%;
            overflow: hidden; margin: 0 auto 14px;
            border: 2.5px solid var(--accent);
            box-shadow: 0 4px 16px rgba(34, 211, 238, 0.25);
            background: #0A1628; display: flex; align-items: center; justify-content: center;
        }
        .head-logo-circle img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .head h1 { font-size: 24px; font-weight: 800; color: #E6EEF7; letter-spacing: -0.5px; }
        .head h1 span { color: var(--accent); }
        .head p { font-size: 11.5px; color: var(--muted); font-weight: 800; text-transform: uppercase; letter-spacing: 1px; margin-top: 4px; }

        .body { padding: 22px 32px 32px; }
        
        .notice {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px; font-weight: 600;
            background: rgba(34, 211, 238, 0.12); color: #22D3EE; border-left: 4px solid var(--accent);
            display: flex; align-items: center; gap: 8px;
        }

        .alert-danger {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px; font-weight: 600;
            background: rgba(248, 113, 113, 0.18); color: #F87171; border-left: 4px solid #DC2626;
            display: flex; align-items: center; gap: 8px;
        }

        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 11.5px; font-weight: 800; color: var(--muted);
            margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.8px;
        }
        .form-control {
            width: 100%; padding: 13px 16px; border: 1.5px solid var(--border); border-radius: 14px;
            font-size: 14px; font-weight: 600; background: #0F1D33; color: var(--ink); outline: none;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.2);
        }

        .btn {
            width: 100%; padding: 14px; background: var(--accent-blue); color: #FFFFFF; border: none; border-radius: var(--radius-pill);
            font-size: 15px; font-weight: 800; cursor: pointer; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn:hover {
            background: var(--accent-blue-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }
        .out { text-align: center; margin-top: 18px; }
        .out button {
            background: none; border: none; color: var(--muted); font-size: 13px; font-weight: 700; cursor: pointer;
            transition: color 0.15s;
        }
        .out button:hover { color: #F87171; }
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