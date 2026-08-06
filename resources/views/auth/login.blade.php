<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Pendaftaran Rawat Jalan RSI Sakinah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0F766E;
            --primary-dark: #0B544E;
            --islamic-green: #15803D;
            --white: #FFFFFF;
            --soft-gray: #F1F5F4;
            --light-green: #ECFDF5;
            --gold: #D4AF37;
            --sky: #0EA5E9;
            --ink: #1E293B;
            --muted: #64748B;
            --danger: #DC2626;
            --radius-lg: 28px;
            --radius-md: 16px;
            --radius-sm: 12px;
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
            background: var(--soft-gray);
            color: var(--ink);
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.15fr 1fr;
        }

        /* ---------- LEFT PANEL ---------- */
        /*
         * Ilustrasi gedung di sini pakai SVG (dari kode awal kamu) sebagai fallback yang
         * selalu tampil rapi. Kalau nanti kamu punya foto asli gedung RSI Sakinah, tinggal
         * uncomment baris background-image di bawah dan arahkan ke path foto kamu — SVG-nya
         * bisa dihapus atau dibiarkan (akan otomatis tertutup foto).
         */
        .auth-visual {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            padding: 56px 64px;
            background:
                linear-gradient(105deg, rgba(248, 250, 252, .94) 0%, rgba(236, 253, 245, .88) 42%, rgba(21, 128, 61, .30) 100%);
            /* background-image: linear-gradient(105deg, rgba(248,250,252,.94) 0%, rgba(236,253,245,.62) 40%, rgba(21,128,61,.35) 100%), url('/images/gedung-rsi-sakinah.jpg');
               background-size: cover; background-position: left bottom; */
        }

        .auth-visual .building {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .auth-visual .building svg {
            position: absolute;
            bottom: 0;
            left: -20px;
            width: 52%;
            height: 88%;
        }

        .auth-visual .deco-plus {
            position: absolute;
            color: rgba(15, 118, 110, .18);
            font-weight: 800;
            pointer-events: none;
        }

        .auth-visual .deco-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(15, 118, 110, .15);
            pointer-events: none;
        }

        .visual-content {
            position: relative;
            z-index: 2;
            max-width: 460px;
            margin-left: auto;
        }

        .visual-content .eyebrow {
            font-size: 1.05rem;
            color: #334155;
            font-weight: 500;
            margin-bottom: 2px;
        }

        .visual-content h1 {
            font-size: 2.35rem;
            font-weight: 800;
            line-height: 1.15;
            margin: 0 0 18px;
            background: linear-gradient(120deg, var(--primary-dark), var(--islamic-green));
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .visual-content .lead {
            font-size: 1rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .visual-content .desc {
            font-size: .92rem;
            color: var(--muted);
            margin-bottom: 32px;
            line-height: 1.6;
        }

        .feature-row {
            display: flex;
            gap: 14px;
            margin-bottom: 22px;
        }

        .feature-icon {
            flex: none;
            width: 44px;
            height: 44px;
            border-radius: 13px;
            background: linear-gradient(135deg, var(--primary), var(--islamic-green));
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            font-size: 1.1rem;
            box-shadow: 0 6px 16px rgba(15, 118, 110, .25);
        }

        .feature-text strong {
            display: block;
            font-size: .92rem;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .feature-text span {
            font-size: .82rem;
            color: var(--muted);
            line-height: 1.5;
        }

        .help-float {
            position: absolute;
            left: 64px;
            bottom: 40px;
            z-index: 2;
            background: rgba(255, 255, 255, .9);
            backdrop-filter: blur(6px);
            border-radius: var(--radius-md);
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 14px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, .1);
        }

        .help-float .icon {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: var(--light-green);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
        }

        .help-float strong {
            display: block;
            font-size: .84rem;
            color: var(--ink);
        }

        .help-float a {
            font-size: .82rem;
            color: var(--primary);
            font-weight: 700;
            text-decoration: none;
        }

        /* ---------- RIGHT PANEL ---------- */
        .auth-form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--soft-gray);
            padding: 40px 32px;
        }

        .auth-card {
            width: 100%;
            max-width: 460px;
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 40px 40px 32px;
            box-shadow:
                0 30px 60px -25px rgba(15, 23, 42, .18),
                0 10px 24px -8px rgba(15, 23, 42, .08);
        }

        .brand-mark {
            display: flex;
            justify-content: center;
            margin-bottom: 18px;
        }

        .brand-mark .ring {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--light-green), var(--white));
            border: 1.5px solid #BBF7D0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }

        .brand-mark svg {
            width: 42px;
            height: 42px;
        }

        .auth-card h2 {
            text-align: center;
            font-size: 1.4rem;
            font-weight: 800;
            margin: 0 0 6px;
        }

        .auth-card .sub {
            text-align: center;
            font-size: .87rem;
            color: var(--muted);
            margin-bottom: 24px;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #334155;
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
            color: #94A3B8;
            font-size: 1rem;
            z-index: 5;
            transition: color .2s;
        }

        .input-group-icon:focus-within .icon {
            color: var(--primary);
        }

        .form-control {
            border-radius: var(--radius-sm);
            border: 1.5px solid #E2E8F0;
            padding: 12px 16px 12px 46px;
            font-size: .9rem;
            font-family: inherit;
            transition: all .2s;
            min-height: 48px;
            background: var(--soft-gray);
        }

        .form-control:focus {
            border-color: var(--primary);
            background: var(--white);
            box-shadow: 0 0 0 4px rgba(15, 118, 110, .12);
        }

        .input-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
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
            background: linear-gradient(120deg, var(--primary-dark) 0%, var(--islamic-green) 100%);
            color: var(--white);
            border: none;
            border-radius: var(--radius-sm);
            padding: 14px;
            font-weight: 700;
            font-size: .95rem;
            width: 100%;
            min-height: 48px;
            transition: transform .18s, box-shadow .18s;
            box-shadow: 0 4px 14px rgba(15, 118, 110, .3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(15, 118, 110, .38);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login:focus-visible,
        .btn-demo:focus-visible {
            outline: 3px solid var(--sky);
            outline-offset: 2px;
        }

        .btn-demo {
            width: 100%;
            min-height: 48px;
            border-radius: var(--radius-sm);
            border: 1.5px solid #E2E8F0;
            background: var(--white);
            color: var(--ink);
            font-weight: 700;
            font-size: .88rem;
            transition: all .18s;
        }

        .btn-demo:hover {
            border-color: var(--primary);
            background: var(--light-green);
            color: var(--primary-dark);
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
            background: #E2E8F0;
        }

        .form-check-input:checked {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .form-check-input:focus {
            box-shadow: 0 0 0 3px rgba(15, 118, 110, .15);
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
            background: var(--light-green);
            color: #14532D;
            border-left: 4px solid var(--islamic-green);
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
                    <!-- ground shadow -->
                    <ellipse cx="210" cy="550" rx="200" ry="10" fill="#0B544E" opacity=".08"/>

                    <!-- low side wing -->
                    <rect x="20" y="360" width="140" height="190" fill="#CBD5E1" opacity=".55" rx="3"/>
                    <g fill="#0F766E" opacity=".35">
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

                    <!-- main tower -->
                    <rect x="120" y="70" width="230" height="480" fill="#E2E8F0" opacity=".75" rx="4"/>
                    <rect x="120" y="70" width="230" height="480" fill="none" stroke="#0F766E" stroke-opacity=".25" stroke-width="2" rx="4"/>

                    <!-- window grid -->
                    <g fill="#15803D" opacity=".32">
                        <rect x="136" y="90" width="26" height="20" rx="2"/><rect x="170" y="90" width="26" height="20" rx="2"/><rect x="204" y="90" width="26" height="20" rx="2"/><rect x="238" y="90" width="26" height="20" rx="2"/><rect x="272" y="90" width="26" height="20" rx="2"/><rect x="306" y="90" width="26" height="20" rx="2"/>
                        <rect x="136" y="120" width="26" height="20" rx="2"/><rect x="170" y="120" width="26" height="20" rx="2"/><rect x="204" y="120" width="26" height="20" rx="2"/><rect x="238" y="120" width="26" height="20" rx="2"/><rect x="272" y="120" width="26" height="20" rx="2"/><rect x="306" y="120" width="26" height="20" rx="2"/>
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
                    <rect x="215" y="30" width="10" height="34" fill="#D4AF37"/>
                    <rect x="203" y="42" width="34" height="10" fill="#D4AF37"/>

                    <!-- entrance canopy + glass door -->
                    <rect x="190" y="460" width="80" height="90" fill="#0B544E" opacity=".55" rx="2"/>
                    <rect x="150" y="450" width="160" height="12" fill="#15803D" opacity=".6" rx="2"/>

                    <!-- ground line -->
                    <rect x="0" y="550" width="420" height="10" fill="#15803D" opacity=".28"/>
                </svg>
            </div>
            <div class="deco-ring" style="width:260px;height:260px;top:-60px;left:20%;"></div>
            <div class="deco-ring" style="width:160px;height:160px;bottom:80px;right:8%;"></div>
            <i class="bi bi-plus-lg deco-plus" style="font-size:1.4rem;top:14%;left:28%;"></i>
            <i class="bi bi-plus-lg deco-plus" style="font-size:1.1rem;bottom:32%;right:22%;"></i>

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
                        <label class="form-check-label" for="remember" style="font-size:.82rem;color:#64748B;">
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