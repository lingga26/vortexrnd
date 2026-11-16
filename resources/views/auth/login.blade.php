<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | CMS Digital Signage</title>
    <!-- Favicon & Brand Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') . '?v=' . filemtime(public_path('images/logo.png')) }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo.png') . '?v=' . filemtime(public_path('images/logo.png')) }}">
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

        /* Ambient Glows bernuansa Modern Cyan & Royal Blue Tech */
        body::before, body::after {
            content: ""; position: absolute; border-radius: 50%; z-index: 1; filter: blur(120px); pointer-events: none;
        }
        body::before {
            width: 450px; height: 450px;
            background: rgba(34, 211, 238, 0.12);
            top: -100px; right: -80px;
        }
        body::after {
            width: 420px; height: 420px;
            background: rgba(37, 99, 235, 0.15);
            bottom: -100px; left: -80px;
        }

        .login-container {
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

        /* Strip aksen gradient royal blue ke cyan */
        .login-accent-bar {
            height: 5px;
            width: 100%;
            background: linear-gradient(90deg, #2563EB 0%, #22D3EE 100%);
        }

        .login-header {
            text-align: center;
            padding: 34px 24px 10px 24px;
        }
        .login-logo-circle {
            width: 62px; height: 62px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 16px;
            border: 2.5px solid var(--accent);
            box-shadow: 0 4px 16px rgba(34, 211, 238, 0.25);
            background: #0A1628;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-logo-circle img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .login-header h1 {
            font-size: 26px; font-weight: 800;
            color: #E6EEF7;
            letter-spacing: -0.5px;
        }
        .login-header h1 span {
            color: var(--accent);
        }
        .login-header p {
            font-size: 11px;
            color: var(--muted);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: 4px;
        }

        .login-body {
            padding: 24px 32px 34px 32px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 800;
            color: var(--muted);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }
        
        .input-wrapper { position: relative; }
        .input-wrapper i.input-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: var(--muted); z-index: 3; font-size: 14px;
            transition: color 0.2s ease;
        }
        .form-control {
            width: 100%; padding: 13px 14px 13px 44px;
            border: 1.5px solid var(--border);
            border-radius: 14px;
            font-size: 14px;
            font-weight: 600;
            background: #0F1D33;
            color: var(--ink);
            outline: none;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(34, 211, 238, 0.2);
        }
        .form-control:focus ~ i.input-icon {
            color: var(--accent);
        }

        .password-toggle {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: var(--muted); cursor: pointer; z-index: 4;
            font-size: 14px; padding: 4px; transition: color 0.2s ease;
        }
        .password-toggle:hover {
            color: var(--accent);
        }

        .remember-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 24px; font-size: 13px; color: var(--ink); font-weight: 600;
        }
        .remember-row input[type="checkbox"] {
            accent-color: var(--accent-blue); width: 16px; height: 16px; cursor: pointer;
        }

        /* Lupa password */
        .forgot-link {
            margin-left: auto; color: var(--accent); font-size: 13px; font-weight: 700;
            text-decoration: none; background: none; border: none; cursor: pointer;
            transition: color 0.15s ease;
        }
        .forgot-link:hover {
            color: var(--accent-hover);
            text-decoration: underline;
        }
        .forgot-info {
            display: none; margin-bottom: 20px; padding: 12px 14px; border-radius: 12px;
            background: rgba(37, 99, 235, 0.15); color: #93C5FD;
            border-left: 4px solid var(--accent-blue);
            font-size: 13px; font-weight: 600; align-items: center; gap: 8px;
        }
        .forgot-info.show { display: flex; animation: slideUp 0.25s ease-out; }
        .remember-row.has-info { margin-bottom: 14px; }

        .btn-login {
            width: 100%; padding: 14px;
            background: var(--accent-blue);
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-pill);
            font-size: 15px; font-weight: 800; letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35);
            transition: all 0.2s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover {
            background: var(--accent-blue-hover);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        }

        .login-footer {
            text-align: center; padding: 16px 20px; font-size: 11.5px; font-weight: 600;
            color: var(--muted);
            background: var(--track);
            border-top: 1px solid var(--border);
        }

        .alert {
            padding: 12px 14px; border-radius: 12px; margin-bottom: 18px; font-size: 13px;
            font-weight: 600; display: flex; align-items: center; gap: 8px;
        }
        .alert-danger {
            background: rgba(248, 113, 113, 0.18);
            color: #F87171;
            border-left: 4px solid #DC2626;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-accent-bar"></div>

    <div class="login-header">
        <div class="login-logo-circle">
            <img src="{{ asset('images/logo.png') . '?v=' . filemtime(public_path('images/logo.png')) }}" alt="VortexSign Logo">
        </div>
        <h1>VORTEX<span>SIGN</span></h1>
        <p>Content Management &amp; Monitoring Dashboard</p>
    </div>

    <div class="login-body">
        @if($errors->any())
            <div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> {{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login.post') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="username">Username / Email</label>
                <div class="input-wrapper">
                    <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}" placeholder="Masukkan username" required autofocus>
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrapper">
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                    <i class="fas fa-lock input-icon"></i>
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Lihat Password"><i class="fas fa-eye"></i></button>
                </div>
            </div>

            <div class="remember-row" id="rememberRow">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember" style="cursor:pointer;">Ingat saya</label>
                <button type="button" class="forgot-link" id="forgotLink">Lupa password?</button>
            </div>

            <div class="forgot-info" id="forgotInfo" role="status">
                <i class="fas fa-info-circle"></i>
                <span>Silahkan hubungi admin untuk reset password.</span>
            </div>

            <button type="submit" class="btn-login" id="loginBtn"><i class="fas fa-sign-in-alt"></i> Masuk</button>
        </form>
    </div>
    <div class="login-footer">Version 2026 - 2.2.1 © CMS Digital Signage</div>
</div>

<script>
    const toggleBtn = document.getElementById('togglePassword');
    const passInput = document.getElementById('password');
    const toggleIcon = toggleBtn.querySelector('i');
    toggleBtn.addEventListener('click', () => {
        const isPassword = passInput.type === 'password';
        passInput.type = isPassword ? 'text' : 'password';
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    });

    // Lupa password: toggle keterangan
    const forgotLink = document.getElementById('forgotLink');
    const forgotInfo = document.getElementById('forgotInfo');
    const rememberRow = document.getElementById('rememberRow');
    forgotLink.addEventListener('click', () => {
        const show = forgotInfo.classList.toggle('show');
        rememberRow.classList.toggle('has-info', show);
    });
</script>
</body>
</html>