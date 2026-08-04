<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Sistem Pendaftaran Rawat Jalan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a3a6c; --primary-dark: #0f2347; --accent: #f97316; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh; margin: 0;
            background: linear-gradient(135deg, var(--primary-dark) 0%, #1e4080 50%, #1a3a6c 100%);
            display: flex; align-items: center; justify-content: center;
            padding: 20px;
            position: relative; overflow: hidden;
        }

        /* Decorative circles */
        body::before, body::after {
            content: ''; position: absolute; border-radius: 50%; opacity: .08;
            background: #fff;
        }
        body::before { width: 500px; height: 500px; top: -150px; right: -100px; }
        body::after  { width: 350px; height: 350px; bottom: -100px; left: -80px; }

        .login-wrapper {
            width: 100%; max-width: 440px; position: relative; z-index: 1;
        }
        .login-card {
            background: #fff; border-radius: 24px;
            box-shadow: 0 24px 60px rgba(0,0,0,.25);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--primary) 0%, #2451a0 100%);
            padding: 36px 36px 28px; text-align: center;
        }
        .login-header .logo-ring {
            width: 70px; height: 70px; border-radius: 20px;
            background: var(--accent); display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 2rem; color: #fff; margin-bottom: 16px;
            box-shadow: 0 8px 20px rgba(249,115,22,.4);
        }
        .login-header h1 { color: #fff; font-size: 1.3rem; font-weight: 800; margin: 0 0 4px; }
        .login-header p  { color: rgba(255,255,255,.6); font-size: .82rem; margin: 0; }

        .login-body { padding: 32px 36px 36px; }
        .login-body h2 { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 4px; }
        .login-body .sub { font-size: .83rem; color: #64748b; margin-bottom: 24px; }

        .form-control {
            border-radius: 12px; border: 1.5px solid #e2e8f0;
            padding: 12px 16px 12px 46px; font-size: .875rem;
            transition: all .2s;
        }
        .form-control:focus {
            border-color: var(--primary); box-shadow: 0 0 0 3px rgba(26,58,108,.12);
        }
        .input-group-icon {
            position: relative;
        }
        .input-group-icon .icon {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 1rem; z-index: 5;
        }
        .input-toggle {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            color: #94a3b8; cursor: pointer; z-index: 5; background: none; border: none; padding: 0;
        }
        .form-label { font-size: .8rem; font-weight: 600; color: #475569; margin-bottom: 6px; }

        .btn-login {
            background: linear-gradient(135deg, var(--accent) 0%, #fb923c 100%);
            color: #fff; border: none; border-radius: 12px;
            padding: 13px; font-weight: 700; font-size: .95rem;
            width: 100%; transition: all .2s;
            box-shadow: 0 4px 14px rgba(249,115,22,.35);
        }
        .btn-login:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(249,115,22,.45); }
        .btn-login:active { transform: translateY(0); }

        .alert { border: none; border-radius: 12px; font-size: .84rem; padding: 12px 16px; }
        .alert-danger { background: #fef2f2; color: #991b1b; border-left: 4px solid #ef4444; }

        .demo-info {
            background: linear-gradient(135deg, #eff6ff, #f0f9ff);
            border: 1px solid #bfdbfe; border-radius: 12px;
            padding: 14px 16px; margin-top: 20px; font-size: .79rem; color: #1e40af;
        }
        .demo-info strong { display: block; margin-bottom: 6px; color: #1e3a8a; }
        .demo-row { display: flex; justify-content: space-between; padding: 2px 0; }

        @keyframes slideUp { from { opacity:0; transform: translateY(30px); } to { opacity:1; transform: none; } }
        .login-card { animation: slideUp .5s ease both; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <div class="login-header">
            <div class="logo-ring"><i class="bi bi-hospital-fill"></i></div>
            <h1>Sistem Pendaftaran Rawat Jalan</h1>
            <p>RS Klinik — Manajemen Antrian Digital</p>
        </div>
        <div class="login-body">
            <h2>Selamat Datang 👋</h2>
            <p class="sub">Masuk untuk mengelola pendaftaran pasien</p>

            @if ($errors->any())
                <div class="alert alert-danger mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success mb-3">{{ session('success') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Alamat Email</label>
                    <div class="input-group-icon">
                        <i class="bi bi-envelope icon"></i>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email') }}" placeholder="admin@rsklinik.id" required autofocus>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group-icon">
                        <i class="bi bi-lock icon"></i>
                        <input type="password" name="password" id="password" class="form-control"
                               placeholder="••••••••" required>
                        <button type="button" class="input-toggle" onclick="togglePwd()">
                            <i class="bi bi-eye" id="pwd-icon"></i>
                        </button>
                    </div>
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label" for="remember" style="font-size:.82rem;color:#64748b;">
                        Ingat saya di perangkat ini
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                </button>
            </form>

            <div class="demo-info mt-3">
                <strong><i class="bi bi-info-circle me-1"></i>Akun Demo</strong>
                <div class="demo-row"><span>Admin:</span><span>admin@rsklinik.id / password</span></div>
                <div class="demo-row"><span>Petugas:</span><span>petugas@rsklinik.id / password</span></div>
            </div>
        </div>
    </div>
    <p class="text-center mt-3" style="color:rgba(255,255,255,.4);font-size:.75rem;">
        &copy; {{ date('Y') }} Sistem Pendaftaran Rawat Jalan
    </p>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePwd() {
    const p = document.getElementById('password');
    const i = document.getElementById('pwd-icon');
    p.type = p.type === 'password' ? 'text' : 'password';
    i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
}
</script>
</body>
</html>
