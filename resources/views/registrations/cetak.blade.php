<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Tracer – {{ $registration->nomor_antrian }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --green:        #0F7B63;
            --green-dark:   #0A5644;
            --green-soft:   #E6F6F0;
            --green-mid:    #14966E;
            --gold:         #B8912E;
            --gold-soft:    #FBF6E9;
            --ink:          #0E1A16;
            --muted:        #5A6E67;
            --border:       #D8E8E3;
            --surface:      #FFFFFF;
            --bg:           #F4F8F6;
        }

        @page {
            size: A5 portrait;
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--ink);
            width: 148mm;
            min-height: 210mm;
            margin: 0 auto;
        }

        .page {
            width: 148mm;
            min-height: 210mm;
            background: var(--surface);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* ── HEADER STRIP ── */
        .header {
            background: linear-gradient(135deg, var(--green-dark) 0%, var(--green) 60%, var(--green-mid) 100%);
            padding: 16px 20px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }
        .header::after {
            content: '';
            position: absolute;
            right: -30px; top: -30px;
            width: 120px; height: 120px;
            border-radius: 50%;
            background: rgba(255,255,255,.07);
        }
        .header::before {
            content: '';
            position: absolute;
            right: 30px; top: 20px;
            width: 70px; height: 70px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
        }

        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            position: relative; z-index: 1;
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: rgba(255,255,255,.22);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
            color: #fff;
            flex-shrink: 0;
        }
        .logo-text {
            color: #fff;
        }
        .logo-text .rs-name {
            font-family: 'Amiri', serif;
            font-size: .95rem;
            font-weight: 700;
            line-height: 1.2;
            letter-spacing: .01em;
        }
        .logo-text .rs-tagline {
            font-size: .6rem;
            opacity: .75;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .header-meta {
            text-align: right;
            color: rgba(255,255,255,.85);
            font-size: .6rem;
            font-weight: 500;
            position: relative; z-index: 1;
        }
        .header-meta strong {
            display: block;
            font-size: .7rem;
            color: #fff;
            font-weight: 700;
        }

        /* ── DIVIDER SAWTOOTH / PERFORATED ── */
        .perforated {
            height: 14px;
            background: linear-gradient(135deg, var(--bg) 25%, transparent 25%),
                        linear-gradient(225deg, var(--bg) 25%, transparent 25%);
            background-size: 14px 14px;
            background-color: var(--green);
            background-position: 0 0, 7px 0;
        }

        /* ── NOMOR ANTRIAN HERO ── */
        .antrian-hero {
            padding: 16px 20px 10px;
            text-align: center;
            background: var(--green-soft);
            border-bottom: 1.5px dashed var(--border);
            position: relative;
        }
        .antrian-label {
            font-size: .58rem;
            font-weight: 700;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: var(--green);
            margin-bottom: 2px;
        }
        .antrian-number {
            font-family: 'Amiri', serif;
            font-size: 3.2rem;
            font-weight: 700;
            color: var(--green-dark);
            line-height: 1;
            letter-spacing: .01em;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: .6rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .1em;
            color: var(--green);
            padding: 10px 20px 4px;
            border-bottom: 1px solid var(--green-soft);
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .section-title svg {
            width: 11px; height: 11px;
            fill: var(--green);
            flex-shrink: 0;
        }

        /* ── INFO GRID ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            padding: 8px 20px 12px;
            gap: 0;
        }
        .info-item {
            padding: 6px 0;
            border-bottom: 1px solid #EEF4F1;
        }
        .info-item.full {
            grid-column: 1 / -1;
        }
        .info-item:last-child, .info-item.full:last-child {
            border-bottom: none;
        }
        .info-label {
            font-size: .56rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: var(--muted);
            margin-bottom: 2px;
        }
        .info-value {
            font-size: .76rem;
            font-weight: 700;
            color: var(--ink);
            line-height: 1.3;
        }
        .info-value.mono {
            font-family: 'Courier New', monospace;
            color: var(--green);
            font-size: .72rem;
        }
        .info-value.badge {
            display: inline-block;
            background: var(--green-soft);
            color: var(--green-dark);
            padding: 2px 9px;
            border-radius: 5px;
            font-size: .66rem;
        }

        /* ── PERFORATED SEPARATOR ── */
        .separator-dashed {
            border: none;
            border-top: 2px dashed var(--border);
            margin: 0 20px;
        }

        /* ── FOOTER ── */
        .footer {
            margin-top: auto;
            padding: 10px 20px 14px;
            border-top: 1.5px solid var(--green-soft);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .footer-msg {
            font-size: .65rem;
            color: var(--muted);
            line-height: 1.5;
        }
        .footer-msg strong {
            display: block;
            color: var(--ink);
            font-size: .67rem;
            margin-bottom: 2px;
        }
        .footer-qr {
            width: 52px; height: 52px;
            background: var(--green-soft);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .footer-qr svg {
            width: 32px; height: 32px;
        }

        /* ── WATERMARK ── */
        .watermark {
            position: absolute;
            bottom: 70px; right: 18px;
            font-family: 'Amiri', serif;
            font-size: 4rem;
            font-weight: 700;
            color: rgba(15, 123, 99, .04);
            letter-spacing: .1em;
            pointer-events: none;
            user-select: none;
            white-space: nowrap;
        }

        /* ── BOTTOM STRIP ── */
        .bottom-strip {
            height: 5px;
            background: linear-gradient(90deg, var(--green-dark), var(--green), var(--gold), var(--green));
        }

        /* ── PRINT ── */
        @media print {
            html, body { margin: 0; padding: 0; background: #fff; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }
            .page { box-shadow: none; border-radius: 0; }
            .header {
                background: linear-gradient(135deg, #0A5644 0%, #0F7B63 60%, #14966E 100%) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .antrian-hero {
                background: #E6F6F0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .perforated {
                background: linear-gradient(135deg, #F4F8F6 25%, transparent 25%),
                            linear-gradient(225deg, #F4F8F6 25%, transparent 25%) !important;
                background-size: 14px 14px !important;
                background-color: #0F7B63 !important;
                background-position: 0 0, 7px 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .bottom-strip {
                background: linear-gradient(90deg, #0A5644, #0F7B63, #B8912E, #0F7B63) !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .info-value.badge {
                background: #E6F6F0 !important;
                color: #0A5644 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .section-title {
                color: #0F7B63 !important;
                border-bottom-color: #E6F6F0 !important;
            }
        }

        /* Screen preview styling */
        @media screen {
            body { min-height: 100vh; padding: 24px 0; background: #e5eae8; }
            .page {
                box-shadow: 0 8px 40px rgba(0,0,0,.18);
                border-radius: 4px;
                overflow: hidden;
            }
            .no-print-bar {
                position: fixed;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background: var(--green-dark);
                color: #fff;
                padding: 10px 28px;
                border-radius: 999px;
                font-family: 'Plus Jakarta Sans', sans-serif;
                font-size: .82rem;
                font-weight: 700;
                cursor: pointer;
                box-shadow: 0 4px 20px rgba(10,86,68,.4);
                display: flex;
                align-items: center;
                gap: 8px;
                border: none;
                z-index: 999;
            }
            .no-print-bar:hover { background: var(--green); }
        }
        @media print {
            .no-print-bar { display: none; }
        }
    </style>
</head>
<body>
    <div class="page">

        {{-- ── WATERMARK ── --}}
        <div class="watermark">RSI</div>

        {{-- ── HEADER ── --}}
        <div class="header">
            <div class="header-logo">
                <div class="logo-icon">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" width="22" height="22">
                        <path d="M12 2L4 6V12C4 15.31 7.58 18.8 12 20C16.42 18.8 20 15.31 20 12V6L12 2Z" fill="rgba(255,255,255,.3)" stroke="#fff" stroke-width="1.5"/>
                        <path d="M11 8H13V11H16V13H13V16H11V13H8V11H11V8Z" fill="#fff"/>
                    </svg>
                </div>
                <div class="logo-text">
                    <div class="rs-name">RS Islam Sakinah</div>
                    <div class="rs-tagline">Melayani dengan Sepenuh Hati</div>
                </div>
            </div>
            <div class="header-meta">
                <strong>TIKET ANTRIAN</strong>
                Rawat Jalan<br>
                {{ now()->translatedFormat('d F Y') }}<br>
                {{ now()->format('H:i') }} WIB
            </div>
        </div>

        {{-- ── PERFORATED EDGE ── --}}
        <div class="perforated"></div>

        {{-- ── NOMOR ANTRIAN ── --}}
        <div class="antrian-hero">
            <div class="antrian-label">Nomor Antrian</div>
            <div class="antrian-number">{{ $registration->nomor_antrian }}</div>
        </div>

        {{-- ── INFO PASIEN ── --}}
        <div class="section-title">
            <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M10 10a4 4 0 100-8 4 4 0 000 8zm-7 9a7 7 0 0114 0H3z"/></svg>
            Informasi Pasien
        </div>
        <div class="info-grid">
            <div class="info-item full">
                <div class="info-label">Nama Pasien</div>
                <div class="info-value">{{ $registration->patient->nama_pasien }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">No. Rekam Medis</div>
                <div class="info-value mono">{{ $registration->patient->no_rm }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">NIK</div>
                <div class="info-value mono" style="font-size:.64rem;">{{ $registration->patient->nik }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Lahir</div>
                <div class="info-value" style="font-weight:500;">
                    {{ $registration->patient->tanggal_lahir->translatedFormat('d M Y') }}
                    <span style="color:var(--muted); font-size:.66rem;">({{ $registration->patient->umur }} thn)</span>
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Jenis Kelamin</div>
                <div class="info-value" style="font-weight:500;">{{ $registration->patient->jenis_kelamin_label }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Jenis Pembayaran</div>
                <div class="info-value badge">{{ strtoupper($registration->patient->jenis_pembayaran) }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">No. Telepon</div>
                <div class="info-value" style="font-weight:500;">{{ $registration->patient->no_telepon ?? '-' }}</div>
            </div>
        </div>

        <hr class="separator-dashed">

        {{-- ── INFO KUNJUNGAN ── --}}
        <div class="section-title">
            <svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5h8a1 1 0 010 2H6a1 1 0 010-2z"/></svg>
            Informasi Kunjungan
        </div>
        <div class="info-grid">
            <div class="info-item">
                <div class="info-label">Poli Tujuan</div>
                <div class="info-value">{{ $registration->department->nama_poli }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Tanggal Kunjungan</div>
                <div class="info-value" style="font-weight:500;">{{ $registration->tanggal_kunjungan->translatedFormat('d F Y') }}</div>
            </div>
            <div class="info-item full">
                <div class="info-label">Dokter</div>
                <div class="info-value">{{ $registration->doctor->nama_dokter }}</div>
            </div>
            <div class="info-item">
                <div class="info-label">Jadwal</div>
                <div class="info-value" style="font-weight:500;">
                    {{ $registration->tanggal_kunjungan->translatedFormat('d F Y') }} <br>
                    {{ $registration->doctorSchedule->hari }},
                    {{ substr($registration->doctorSchedule->jam_mulai,0,5) }}–{{ substr($registration->doctorSchedule->jam_selesai,0,5) }}
                </div>
            </div>
            <div class="info-item">
                <div class="info-label">Kode Booking</div>
                <div class="info-value" style="font-size:1.05rem; color:var(--green-dark);">
                    {{ $registration->kode_booking ?? str_pad($registration->urutan_antrian, 6, '0', STR_PAD_LEFT) }}
                </div>
            </div>
            @if($registration->keluhan)
            <div class="info-item full">
                <div class="info-label">Keluhan Utama</div>
                <div class="info-value" style="font-weight:400;">{{ $registration->keluhan }}</div>
            </div>
            @endif
        </div>

        {{-- ── FOOTER ── --}}
        @php
            $hasKodeBooking = !empty($registration->kode_booking);
            $infoUrl = route('info.pendaftaran');
            $qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&format=png&data=' . urlencode($infoUrl);
        @endphp
        <div class="footer">
            <div class="footer-msg">
                <strong>Petunjuk Pasien</strong>
                Harap menunggu di ruang tunggu poli yang bersangkutan.<br>
                Patuhi protokol kesehatan &amp; antri sesuai nomor.
                <br><span style="color:var(--green); font-weight:600;">Scan QR untuk portal layanan pasien.</span>
            </div>
            <div style="text-align:center; flex-shrink:0;">
                <img src="{{ $qrUrl }}" alt="QR Info Pendaftaran" width="60" height="60" style="border-radius:6px; border:1px solid var(--border);"><br>
                <span style="font-size:.5rem; color:var(--muted); display:block; margin-top:3px;">Scan QR Code</span>
            </div>
        </div>

        <div class="bottom-strip"></div>

    </div>

    <button class="no-print-bar" onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zm4 11h-2v-1h2v1zm0-3h-2V7h2v2zM5 3h6v2H5V3z"/>
        </svg>
        Cetak Sekarang
    </button>

    <script>
        // Auto-print saat halaman dibuka
        window.addEventListener('load', function() {
            // sedikit delay agar font Google Fonts termuat dulu
            setTimeout(function() {
                window.print();
            }, 800);
        });
    </script>
</body>
</html>
