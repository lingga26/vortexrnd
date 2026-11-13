<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | CMS Digital Signage</title>
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
            --accent: #A06841;           /* coklat karamel / toffee */
            --accent-hover: #86532F;
            --accent-soft: #DFCEBC;      /* cream biscuit */
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

        /* Ambient Glows bernuansa Coklat Hangat & Karamel */
        body::before, body::after {
            content: ""; position: absolute; border-radius: 50%; z-index: 1; filter: blur(120px); pointer-events: none;
        }
        body::before {
            width: 450px; height: 450px;
            background: rgba(186, 143, 110, 0.16);
            top: -100px; right: -80px;
        }
        body::after {
            width: 420px; height: 420px;
            background: rgba(45, 24, 16, 0.14);
            bottom: -100px; left: -80px;
        }
        [data-theme="dark"] body::before {
            background: rgba(212, 163, 115, 0.10);
        }
        [data-theme="dark"] body::after {
            background: rgba(45, 24, 16, 0.25);
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

        /* Strip aksen gradient coklat - karamel - cream */
        .login-accent-bar {
            height: 5px;
            width: 100%;
            background: linear-gradient(90deg, #2D1810 0%, #A06841 50%, #DFCEBC 100%);
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
            border: 2.5px solid #BA8F6E;
            box-shadow: 0 4px 16px rgba(45, 24, 16, 0.25);
            background: #2D1810;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-logo-circle img {
            width: 100%; height: 100%; object-fit: cover; display: block;
        }
        .login-header h1 {
            font-size: 26px; font-weight: 800;
            color: #2D1810;
            letter-spacing: -0.5px;
        }
        [data-theme="dark"] .login-header h1 {
            color: #FBF6F0;
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
            background: var(--bg-card);
            color: var(--ink);
            outline: none;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(160, 104, 65, 0.22);
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
            accent-color: var(--accent); width: 16px; height: 16px; cursor: pointer;
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
            background: var(--badge-up-bg); color: var(--badge-up-fg);
            border-left: 4px solid var(--accent);
            font-size: 13px; font-weight: 600; align-items: center; gap: 8px;
        }
        .forgot-info.show { display: flex; animation: slideUp 0.25s ease-out; }
        .remember-row.has-info { margin-bottom: 14px; }

        .btn-login {
            width: 100%; padding: 14px;
            background: #2D1810;
            color: #FFFFFF;
            border: none;
            border-radius: var(--radius-pill);
            font-size: 15px; font-weight: 800; letter-spacing: 0.3px;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(45, 24, 16, 0.35);
            transition: all 0.2s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-login:hover {
            background: var(--accent);
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(160, 104, 65, 0.45);
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
            background: var(--badge-down-bg);
            color: var(--badge-down-fg);
            border-left: 4px solid #B85D43;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-accent-bar"></div>

    <div class="login-header">
        <div class="login-logo-circle">
            <img src="{{ asset('images/logo.png') }}" alt="VortexSign Logo">
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
