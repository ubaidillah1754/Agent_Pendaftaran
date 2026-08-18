<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian {{ $department->nama_poli }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Spectral:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --ink: #052E22;
            --emerald: #0B6B4F;
            --emerald-lite: #12946E;
            --gold: #C9A227;
            --gold-lite: #F0D78C;
            --cream: #FBF8F0;
            --teal: #0E7490;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(ellipse 900px 500px at 15% -10%, rgba(18, 148, 110, .35), transparent 60%),
                radial-gradient(ellipse 700px 500px at 105% 10%, rgba(201, 162, 39, .14), transparent 60%),
                linear-gradient(165deg, var(--ink) 0%, #063D2C 55%, var(--ink) 100%);
            color: var(--cream);
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }

        /* Geometric tile texture, like mosque tilework */
        body::before {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='84' height='84'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.07'%3E%3Cpath d='M42 4 L80 42 L42 80 L4 42 Z'/%3E%3Ccircle cx='42' cy='42' r='18'/%3E%3C/g%3E%3C/svg%3E");
        }

        .header,
        .main,
        .footer {
            position: relative;
            z-index: 1;
        }

        /* ===== Header ===== */
        .header {
            padding: 20px 44px;
            border-bottom: 1px solid rgba(232, 199, 102, .2);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .header .brand {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .header .logo-badge {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 6px;
            border: 2px solid var(--gold-lite);
            flex-shrink: 0;
            box-shadow: 0 0 0 5px rgba(201, 162, 39, .08);
        }

        .header .logo-badge img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .header h1 {
            margin: 0;
            font-family: 'Amiri', serif;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: .01em;
        }

        .header .tagline {
            font-size: .7rem;
            color: rgba(251, 248, 240, .45);
            margin-top: 1px;
            font-style: italic;
        }

        .header .poli {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .14em;
            color: var(--gold-lite);
            margin-top: 4px;
        }

        .clock-wrap {
            text-align: right;
        }

        .clock {
            font-family: 'Spectral', serif;
            font-variant-numeric: tabular-nums;
            font-size: 2.1rem;
            font-weight: 700;
            color: var(--gold-lite);
            line-height: 1;
        }

        .date-str {
            font-size: .78rem;
            color: rgba(251, 248, 240, .5);
            margin-top: 4px;
        }

        /* ===== Main split ===== */
        .main {
            flex: 1;
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 0;
        }

        .panel-left {
            padding: 36px 44px 44px;
            border-right: 1px solid rgba(232, 199, 102, .16);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            gap: 6px;
        }

        .panel-right {
            padding: 36px 44px 44px;
            display: flex;
            flex-direction: column;
        }

        .eyebrow {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .18em;
            color: rgba(251, 248, 240, .5);
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
        }

        .eyebrow i {
            color: var(--gold-lite);
        }

        /* ===== Signature element: mihrab arch call panel ===== */
        .arch-stage {
            position: relative;
            width: 340px;
            height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .arch-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(232, 199, 102, .22);
            animation: pulse-ring 2.8s ease-out infinite;
        }

        .arch-ring.r1 {
            inset: 0;
        }

        .arch-ring.r2 {
            inset: 0;
            animation-delay: .9s;
        }

        .arch-ring.r3 {
            inset: 0;
            animation-delay: 1.8s;
        }

        @keyframes pulse-ring {
            0% {
                transform: scale(.72);
                opacity: 0;
            }

            25% {
                opacity: .9;
            }

            100% {
                transform: scale(1.18);
                opacity: 0;
            }
        }

        .arch-frame {
            position: relative;
            z-index: 2;
            width: 250px;
            height: 280px;
            background: linear-gradient(180deg, rgba(232, 199, 102, .10), rgba(232, 199, 102, .02));
            border: 1.5px solid var(--gold);
            border-radius: 125px 125px 14px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 60px rgba(201, 162, 39, .18), inset 0 0 30px rgba(0, 0, 0, .15);
        }

        .arch-frame::before {
            content: "";
            position: absolute;
            inset: 10px;
            border: 1px solid rgba(232, 199, 102, .35);
            border-radius: 115px 115px 8px 8px;
            pointer-events: none;
        }

        .current-number {
            font-family: 'Spectral', serif;
            font-size: 5.6rem;
            font-weight: 800;
            line-height: 1;
            color: var(--gold-lite);
            text-shadow: 0 0 40px rgba(201, 162, 39, .5);
        }

        .current-tag {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .16em;
            color: rgba(251, 248, 240, .55);
            margin-top: 10px;
        }

        .current-name {
            font-size: 1.55rem;
            font-weight: 700;
            margin-top: 20px;
            font-family: 'Amiri', serif;
        }

        .current-doctor {
            font-size: .85rem;
            color: rgba(251, 248, 240, .55);
            margin-top: 4px;
        }

        .current-meta {
            font-size: .85rem;
            color: var(--gold-lite);
            margin-top: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .no-antrian {
            color: rgba(251, 248, 240, .4);
        }

        .no-antrian i {
            font-size: 3.6rem;
            display: block;
            margin-bottom: 14px;
            color: rgba(232, 199, 102, .4);
        }

        /* ===== Next-up ticket list ===== */
        .next-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow: hidden;
        }

        .next-item {
            background: linear-gradient(90deg, rgba(255, 255, 255, .055), rgba(255, 255, 255, .02));
            border: 1px solid rgba(232, 199, 102, .14);
            border-left: 3px solid var(--gold);
            border-radius: 12px;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .next-no {
            font-family: 'Spectral', serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gold-lite);
            min-width: 56px;
            text-align: center;
        }

        .next-divider {
            width: 1px;
            height: 26px;
            background: rgba(232, 199, 102, .2);
        }

        .next-name {
            font-size: 1rem;
            font-weight: 600;
        }

        .next-doctor {
            font-size: .76rem;
            color: rgba(251, 248, 240, .45);
            margin-top: 2px;
        }

        .next-item:first-child {
            border-left-color: var(--emerald-lite);
            box-shadow: 0 0 22px rgba(18, 148, 110, .12);
        }

        .empty-state {
            text-align: center;
            color: rgba(251, 248, 240, .35);
            margin-top: 50px;
        }

        .empty-state i {
            font-size: 3rem;
            display: block;
            margin-bottom: 10px;
        }

        /* ===== Footer ===== */
        .footer {
            padding: 12px 44px;
            border-top: 1px solid rgba(232, 199, 102, .16);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .76rem;
            color: rgba(251, 248, 240, .4);
        }

        .footer .refresh-dot {
            display: inline-block;
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--gold-lite);
            margin-right: 7px;
            animation: blink-dot 1.6s ease-in-out infinite;
        }

        @keyframes blink-dot {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .25
            }
        }

        .footer-accred {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .footer-accred span {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .footer-accred i {
            color: var(--gold-lite);
        }

        @media (prefers-reduced-motion: reduce) {

            .arch-ring,
            .footer .refresh-dot {
                animation: none;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="brand">
            <div class="logo-badge">
                <img src="https://i.ibb.co.com/wmvGtC3/logo-sakinah.png" alt="Logo RS Islam Sakinah">
            </div>
            <div>
                <h1>RS Islam &ldquo;Sakinah&rdquo;</h1>
                <div class="tagline">Layanan kesehatan yang amanah dan terpercaya</div>
                <div class="poli">Poli {{ $department->nama_poli }}</div>
            </div>
        </div>
        <div class="clock-wrap">
            <div class="clock" id="clock">--:--:--</div>
            <div class="date-str" id="date-str"></div>
        </div>
    </div>

    <div class="main">
        <!-- Sedang Dipanggil -->
        <div class="panel-left">
            <div class="eyebrow"><i class="bi bi-megaphone-fill"></i>Sedang Dipanggil</div>

            @if($sedangDipanggil)
                <div class="arch-stage">
                    <div class="arch-ring r1"></div>
                    <div class="arch-ring r2"></div>
                    <div class="arch-ring r3"></div>
                    <div class="arch-frame">
                        <div class="current-number">{{ $sedangDipanggil->nomor_antrian }}</div>
                        <div class="current-tag">Nomor Antrian</div>
                    </div>
                </div>
                <div class="current-name">{{ $sedangDipanggil->patient->nama_pasien }}</div>
                @if($sedangDipanggil->doctor)
                    <div class="current-doctor">dr. {{ $sedangDipanggil->doctor->nama_dokter }}</div>
                @endif
                <div class="current-meta"><i class="bi bi-arrow-right-circle"></i>Silakan menuju ruang periksa</div>
            @else
                <div class="no-antrian">
                    <i class="bi bi-hourglass-split"></i>
                    <p>Belum ada pasien dipanggil</p>
                </div>
            @endif
        </div>

        <!-- Antrian Berikutnya -->
        <div class="panel-right">
            <div class="eyebrow"><i class="bi bi-list-ol"></i>Antrian Berikutnya</div>

            <div class="next-list">
                @forelse($menunggu as $reg)
                    <div class="next-item">
                        <div class="next-no">{{ $reg->nomor_antrian }}</div>
                        <div class="next-divider"></div>
                        <div>
                            <div class="next-name">{{ $reg->patient->nama_pasien }}</div>
                            @if($reg->doctor)
                                <div class="next-doctor">dr. {{ $reg->doctor->nama_dokter }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Tidak ada antrian menunggu</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="footer">
        <span>Sistem Pendaftaran Rawat Jalan &mdash; RS Islam &ldquo;Sakinah&rdquo; Mojokerto</span>
        <div class="footer-accred">
            <span><i class="bi bi-patch-check-fill"></i>Terakreditasi KARS</span>
            <span><i class="bi bi-shield-check"></i>Mitra BPJS</span>
            <span><span class="refresh-dot"></span>Refresh otomatis 15 detik</span>
        </div>
    </div>

    <script>
        function updateClock() {
            const now = new Date();
            document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
            document.getElementById('date-str').textContent = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
        }
        setInterval(updateClock, 1000); updateClock();
        // Auto refresh halaman setiap 15 detik untuk data terbaru
        setInterval(() => window.location.reload(), 15000);
    </script>
</body>

</html>