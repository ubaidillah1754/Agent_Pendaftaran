<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Pendaftaran Rawat Jalan RSI Sakinah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Spectral:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary:      #0B6B4F;
            --primary-dark: #063D2C;
            --primary-light:#12885F;
            --primary-soft: #E9F3EE;
            --gold:         #C9A227;
            --gold-light:   #E8C766;
            --accent-soft:  #FBF6E9;
            --white:        #FFFFFF;
            --bg:           #F7F8FA;
            --ink:          #1B2430;
            --muted:        #6B7684;
            --border:       #E7EAEF;
            --danger:       #DC2626;
            --focus-ring:   #0EA5E9;
            --radius-lg: 20px;
            --radius-md: 14px;
            --radius-sm: 10px;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            margin: 0;
            background: var(--bg);
            color: var(--ink);
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
        }

        /* ---------- LEFT PANEL ---------- */
        .auth-visual {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 56px 64px;
            background:
                radial-gradient(ellipse 700px 420px at 8% -8%, rgba(232,199,102,.14), transparent 60%),
                linear-gradient(150deg, var(--primary-dark) 0%, var(--primary) 100%);
        }

        /* geometric tile texture — same motif as the TV display / monitor pages */
        .auth-visual::before {
            content: "";
            position: absolute; inset: 0; z-index: 0; pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='84' height='84'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.08'%3E%3Cpath d='M42 4 L80 42 L42 80 L4 42 Z'/%3E%3Ccircle cx='42' cy='42' r='18'/%3E%3C/g%3E%3C/svg%3E");
        }

        .auth-visual .building {
            position: absolute;
            inset: 0;
            z-index: 0;
            opacity: .55;
        }

        .auth-visual .building svg {
            position: absolute;
            bottom: 0;
            left: -20px;
            width: 52%;
            height: 88%;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            max-width: 460px;
            margin-left: auto;
        }

        .visual-content .eyebrow {
            font-size: .8rem;
            letter-spacing: .16em;
            text-transform: uppercase;
            color: var(--gold-light);
            font-weight: 700;
            margin-bottom: 8px;
        }

        .visual-content h1 {
            font-family: 'Amiri', serif;
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.15;
            margin: 0 0 16px;
            color: #fff;
        }

        .visual-content .lead {
            font-size: 1rem;
            font-weight: 700;
            color: rgba(255, 255, 255, .92);
            margin-bottom: 6px;
        }

        .visual-content .desc {
            font-size: .9rem;
            color: rgba(255, 255, 255, .68);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .feature-row {
            display: flex;
            gap: 14px;
            margin-bottom: 20px;
        }

        .feature-icon {
            flex: none;
            width: 42px;
            height: 42px;
            border-radius: 21px 21px 6px 6px;
            background: rgba(232, 199, 102, .1);
            border: 1px solid rgba(232, 199, 102, .3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold-light);
            font-size: 1.05rem;
        }

        .feature-text strong {
            display: block;
            font-size: .9rem;
            color: #fff;
            margin-bottom: 2px;
        }

        .feature-text span {
            font-size: .8rem;
            color: rgba(255, 255, 255, .62);
            line-height: 1.5;
        }

        .help-float {
            position: absolute;
            left: 64px;
            bottom: 40px;
            z-index: 2;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(232, 199, 102, .22);
            backdrop-filter: blur(6px);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .help-float .icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(232, 199, 102, .14);
            color: var(--gold-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
        }

        .help-float strong {
            display: block;
            font-size: .82rem;
            color: #fff;
        }

        .help-float a {
            font-size: .8rem;
            color: rgba(255, 255, 255, .8);
            font-weight: 600;
            text-decoration: none;
        }

        /* ---------- RIGHT PANEL ---------- */
        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--bg);
            padding: 40px 32px;
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 40px 40px 32px;
            box-shadow: 0 1px 2px rgba(16,24,32,.04), 0 20px 40px -20px rgba(16,24,32,.14);
        }

        /* arch-topped brand badge — echoes the mihrab motif used across the app */
        .brand-mark {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }

        .brand-mark .ring {
            width: 68px;
            height: 68px;
            border-radius: 34px 34px 8px 8px;
            background: var(--primary-soft);
            border: 1.5px solid var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            box-shadow: 0 0 0 5px rgba(201,162,39,.07);
        }

        .brand-mark svg {
            width: 32px;
            height: 32px;
        }

        .auth-card h2 {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0 0 6px;
            color: var(--ink);
        }

        .auth-card .sub {
            text-align: center;
            font-size: .86rem;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .sub-divider {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 22px;
        }
        .sub-divider span {
            width: 6px; height: 6px; background: var(--gold);
            transform: rotate(45deg);
        }

        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .input-group-icon {
            position: relative;
        }

        .input-group-icon .icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #A2ACB8;
            font-size: 1rem;
            z-index: 5;
            transition: color .2s;
        }

        .input-group-icon:focus-within .icon {
            color: var(--primary);
        }

        .form-control {
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            padding: 12px 16px 12px 46px;
            font-size: .9rem;
            font-family: inherit;
            transition: all .2s;
            min-height: 48px;
            background: var(--bg);
        }

        .form-control:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(11, 107, 79, .12);
        }

        .input-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #A2ACB8;
            cursor: pointer;
            z-index: 5;
            background: none;
            border: none;
            padding: 6px;
            display: flex;
        }

        .input-toggle:hover {
            color: var(--primary);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            padding: 14px;
            font-weight: 700;
            font-size: .95rem;
            width: 100%;
            min-height: 48px;
            transition: all .18s;
            box-shadow: 0 8px 20px -6px rgba(11,107,79,.45);
        }

        .btn-login:hover {
            background: var(--primary-dark);
            box-shadow: 0 8px 20px -6px rgba(11,107,79,.6);
        }

        .btn-login:focus-visible,
        .btn-demo:focus-visible {
            outline: 3px solid var(--focus-ring);
            outline-offset: 2px;
        }

        .btn-demo {
            width: 100%;
            min-height: 48px;
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            background: var(--white);
            color: var(--ink);
            font-weight: 700;
            font-size: .88rem;
            transition: all .18s;
        }

        .btn-demo:hover {
            border-color: var(--gold);
            background: var(--accent-soft);
            color: #8a6c1e;
        }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 22px 0;
            color: var(--muted);
            font-size: .8rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(11, 107, 79, .15);
        }

        .alert {
            border: none;
            border-radius: var(--radius-sm);
            font-size: .84rem;
            padding: 12px 16px;
        }

        .alert-danger {
            background: #FEF2F2;
            color: #991B1B;
            border-left: 4px solid var(--danger);
        }

        .alert-success {
            background: var(--primary-soft);
            color: var(--primary-dark);
            border-left: 4px solid var(--primary);
        }

        .footer-note {
            text-align: center;
            margin-top: 24px;
            color: var(--muted);
            font-size: .78rem;
        }

        @media (max-width: 991px) {
            .auth-shell {
                grid-template-columns: 1fr;
            }

            .auth-visual {
                display: none;
            }

            .auth-form-panel {
                padding: 32px 16px;
            }

            .auth-card {
                padding: 32px 24px 24px;
            }
        }
    </style>
</head>

<body>
    <div class="auth-shell">
        <!-- LEFT: Visual / brand panel -->
        <div class="auth-visual">
            <div class="building" aria-hidden="true">
                <svg viewBox="0 0 420 560" xmlns="http://www.w3.org/2000/svg">
                    <!-- low side wing -->
                    <rect x="20" y="360" width="140" height="190" fill="#ffffff" opacity=".08" rx="3"/>
                    <g fill="#ffffff" opacity=".14">
                        <rect x="36" y="380" width="20" height="26" rx="2"/>
                        <rect x="66" y="380" width="20" height="26" rx="2"/>
                        <rect x="96" y="380" width="20" height="26" rx="2"/>
                        <rect x="126" y="380" width="20" height="26" rx="2"/>
                        <rect x="36" y="418" width="20" height="26" rx="2"/>
                        <rect x="66" y="418" width="20" height="26" rx="2"/>
                        <rect x="96" y="418" width="20" height="26" rx="2"/>
                        <rect x="126" y="418" width="20" height="26" rx="2"/>
                        <rect x="36" y="456" width="20" height="26" rx="2"/>
                        <rect x="66" y="456" width="20" height="26" rx="2"/>
                        <rect x="96" y="456" width="20" height="26" rx="2"/>
                        <rect x="126" y="456" width="20" height="26" rx="2"/>
                    </g>

                    <!-- main tower, arch-topped like a mihrab silhouette -->
                    <path d="M120 130 A115 115 0 0 1 350 130 L350 550 L120 550 Z" fill="#ffffff" opacity=".1"/>
                    <path d="M120 130 A115 115 0 0 1 350 130 L350 550 L120 550 Z" fill="none" stroke="#ffffff" stroke-opacity=".18" stroke-width="2"/>
                    <path d="M120 130 A115 115 0 0 1 350 130" fill="none" stroke="#E8C766" stroke-opacity=".55" stroke-width="2"/>

                    <!-- window grid -->
                    <g fill="#ffffff" opacity=".16">
                        <rect x="136" y="150" width="26" height="20" rx="2"/><rect x="170" y="150" width="26" height="20" rx="2"/><rect x="204" y="150" width="26" height="20" rx="2"/><rect x="238" y="150" width="26" height="20" rx="2"/><rect x="272" y="150" width="26" height="20" rx="2"/><rect x="306" y="150" width="26" height="20" rx="2"/>
                        <rect x="136" y="180" width="26" height="20" rx="2"/><rect x="170" y="180" width="26" height="20" rx="2"/><rect x="204" y="180" width="26" height="20" rx="2"/><rect x="238" y="180" width="26" height="20" rx="2"/><rect x="272" y="180" width="26" height="20" rx="2"/><rect x="306" y="180" width="26" height="20" rx="2"/>
                        <rect x="136" y="210" width="26" height="20" rx="2"/><rect x="170" y="210" width="26" height="20" rx="2"/><rect x="204" y="210" width="26" height="20" rx="2"/><rect x="238" y="210" width="26" height="20" rx="2"/><rect x="272" y="210" width="26" height="20" rx="2"/><rect x="306" y="210" width="26" height="20" rx="2"/>
                        <rect x="136" y="240" width="26" height="20" rx="2"/><rect x="170" y="240" width="26" height="20" rx="2"/><rect x="204" y="240" width="26" height="20" rx="2"/><rect x="238" y="240" width="26" height="20" rx="2"/><rect x="272" y="240" width="26" height="20" rx="2"/><rect x="306" y="240" width="26" height="20" rx="2"/>
                        <rect x="136" y="270" width="26" height="20" rx="2"/><rect x="170" y="270" width="26" height="20" rx="2"/><rect x="204" y="270" width="26" height="20" rx="2"/><rect x="238" y="270" width="26" height="20" rx="2"/><rect x="272" y="270" width="26" height="20" rx="2"/><rect x="306" y="270" width="26" height="20" rx="2"/>
                        <rect x="136" y="300" width="26" height="20" rx="2"/><rect x="170" y="300" width="26" height="20" rx="2"/><rect x="204" y="300" width="26" height="20" rx="2"/><rect x="238" y="300" width="26" height="20" rx="2"/><rect x="272" y="300" width="26" height="20" rx="2"/><rect x="306" y="300" width="26" height="20" rx="2"/>
                        <rect x="136" y="330" width="26" height="20" rx="2"/><rect x="170" y="330" width="26" height="20" rx="2"/><rect x="204" y="330" width="26" height="20" rx="2"/><rect x="238" y="330" width="26" height="20" rx="2"/><rect x="272" y="330" width="26" height="20" rx="2"/><rect x="306" y="330" width="26" height="20" rx="2"/>
                        <rect x="136" y="360" width="26" height="20" rx="2"/><rect x="170" y="360" width="26" height="20" rx="2"/><rect x="204" y="360" width="26" height="20" rx="2"/><rect x="238" y="360" width="26" height="20" rx="2"/><rect x="272" y="360" width="26" height="20" rx="2"/><rect x="306" y="360" width="26" height="20" rx="2"/>
                        <rect x="136" y="390" width="26" height="20" rx="2"/><rect x="170" y="390" width="26" height="20" rx="2"/><rect x="204" y="390" width="26" height="20" rx="2"/><rect x="238" y="390" width="26" height="20" rx="2"/><rect x="272" y="390" width="26" height="20" rx="2"/><rect x="306" y="390" width="26" height="20" rx="2"/>
                        <rect x="136" y="420" width="26" height="20" rx="2"/><rect x="170" y="420" width="26" height="20" rx="2"/><rect x="204" y="420" width="26" height="20" rx="2"/><rect x="238" y="420" width="26" height="20" rx="2"/><rect x="272" y="420" width="26" height="20" rx="2"/><rect x="306" y="420" width="26" height="20" rx="2"/>
                    </g>

                    <!-- rooftop cross sign -->
                    <rect x="215" y="24" width="10" height="34" fill="#E8C766"/>
                    <rect x="203" y="36" width="34" height="10" fill="#E8C766"/>

                    <!-- entrance canopy + glass door -->
                    <rect x="190" y="460" width="80" height="90" fill="#ffffff" opacity=".08" rx="2"/>
                    <rect x="150" y="450" width="160" height="12" fill="#ffffff" opacity=".18" rx="2"/>

                    <!-- ground line -->
                    <rect x="0" y="550" width="420" height="10" fill="#ffffff" opacity=".1"/>
                </svg>
            </div>

            <div class="visual-content">
                <p class="eyebrow">Selamat Datang di</p>
                <h1>RSI Sakinah</h1>
                <p class="lead">Sistem Pendaftaran Rawat Jalan</p>
                <p class="desc">Mudah, cepat, dan aman untuk pelayanan kesehatan yang lebih baik.</p>

                <div class="feature-row">
                    <div class="feature-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="feature-text">
                        <strong>Pelayanan Terpercaya</strong>
                        <span>Sistem aman dan terintegrasi untuk kenyamanan Anda</span>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <div class="feature-text">
                        <strong>Data Terlindungi</strong>
                        <span>Keamanan data pasien menjadi prioritas kami</span>
                    </div>
                </div>
                <div class="feature-row">
                    <div class="feature-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="feature-text">
                        <strong>Akses 24/7</strong>
                        <span>Daftar dan kelola pendaftaran kapan saja, di mana saja</span>
                    </div>
                </div>
            </div>

            <div class="help-float">
                <div class="icon"><i class="bi bi-headset"></i></div>
                <div>
                    <strong>Butuh Bantuan?</strong>
                    <a href="tel:0211234567">Hubungi Kami: (021) 1234 5678</a>
                </div>
            </div>
        </div>

        <!-- RIGHT: Login form panel (kartu putih melayang) -->
        <div class="auth-form-panel">
            <div class="auth-card">
                <div class="brand-mark">
                    <div class="ring">
                        <!-- Logo mandala + plus, ganti dengan logo resmi RSI Sakinah kalau sudah ada file SVG/PNG-nya -->
                        <svg viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <g fill="currentColor">
                                <circle cx="24" cy="9" r="7.2"/>
                                <circle cx="24" cy="39" r="7.2"/>
                                <circle cx="9" cy="24" r="7.2"/>
                                <circle cx="39" cy="24" r="7.2"/>
                                <circle cx="13.5" cy="13.5" r="6.6"/>
                                <circle cx="34.5" cy="13.5" r="6.6"/>
                                <circle cx="13.5" cy="34.5" r="6.6"/>
                                <circle cx="34.5" cy="34.5" r="6.6"/>
                            </g>
                            <circle cx="24" cy="24" r="12.5" fill="#FFFFFF"/>
                            <g fill="currentColor">
                                <rect x="20.8" y="15" width="6.4" height="18" rx="2"/>
                                <rect x="15" y="20.8" width="18" height="6.4" rx="2"/>
                            </g>
                        </svg>
                    </div>
                </div>
                <h2>Masuk ke Sistem</h2>
                <p class="sub">Silakan masuk untuk mengelola pendaftaran pasien</p>
                <div class="sub-divider"><span></span><span></span><span></span></div>

                @if ($errors->any())
                    <div class="alert alert-danger mb-3">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('login.post') }}" method="POST" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Alamat Email</label>
                        <div class="input-group-icon">
                            <i class="bi bi-envelope icon"></i>
                            <input type="email" id="email" name="email"
                                class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}"
                                placeholder="admin@rsisakinah.id" required autofocus>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <div class="input-group-icon">
                            <i class="bi bi-lock icon"></i>
                            <input type="password" name="password" id="password" class="form-control"
                                placeholder="••••••••" required>
                            <button type="button" class="input-toggle" onclick="togglePwd()" aria-label="Tampilkan password">
                                <i class="bi bi-eye" id="pwd-icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember" style="font-size:.82rem;color:var(--muted);">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk ke Sistem
                    </button>
                </form>

                <div class="divider">atau masuk sebagai</div>

                <button type="button" class="btn-demo" onclick="fillDemo()">
                    <i class="bi bi-people-fill me-2"></i>Akun Demo
                </button>

                <p class="footer-note">&copy; {{ date('Y') }} RSI Sakinah. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function togglePwd() {
            const p = document.getElementById('password');
            const i = document.getElementById('pwd-icon');
            p.type = p.type === 'password' ? 'text' : 'password';
            i.className = p.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
        }

        // Isi otomatis kredensial demo admin ke form yang sama — tidak mengubah alur submit/route.
        function fillDemo() {
            document.getElementById('email').value = 'admin@rsisakinah.id';
            document.getElementById('password').value = 'password';
        }
    </script>
</body>

</html>