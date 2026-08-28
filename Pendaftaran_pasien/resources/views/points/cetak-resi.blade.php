<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Penukaran Poin — #{{ str_pad($redemption->id, 5, '0', STR_PAD_LEFT) }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

        :root {
            --primary: #0F7B63;
            --primary-dark: #0A5644;
            --accent: #B8912E;
            --ink: #16211D;
            --muted: #6C7A76;
            --border: #D8E4DF;
            --bg: #F5F8F7;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff;
            color: var(--ink);
            font-size: 11pt;
        }

        /* ── PRINT WRAPPER ─────────────────────────── */
        .tracer-wrap {
            width: 100%;
            max-width: 210mm; /* A5 landscape atau A4 portrait */
            margin: 0 auto;
            padding: 20mm 18mm;
        }

        /* ── HEADER RUMAH SAKIT ────────────────────── */
        .rs-header {
            display: flex;
            align-items: center;
            gap: 16px;
            border-bottom: 3px solid var(--primary);
            padding-bottom: 12px;
            margin-bottom: 16px;
        }

        .rs-logo-box {
            width: 56px;
            height: 56px;
            flex-shrink: 0;
        }

        .rs-header-text h1 {
            font-size: 15pt;
            font-weight: 800;
            color: var(--primary-dark);
            letter-spacing: -0.3px;
            line-height: 1.2;
        }

        .rs-header-text p {
            font-size: 8.5pt;
            color: var(--muted);
            margin-top: 2px;
            line-height: 1.4;
        }

        .tracer-title-box {
            margin-left: auto;
            text-align: right;
        }

        .tracer-title-box .doc-type {
            font-size: 13pt;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tracer-title-box .doc-no {
            font-size: 10pt;
            font-weight: 700;
            color: var(--ink);
            margin-top: 2px;
        }

        .tracer-title-box .doc-date {
            font-size: 8pt;
            color: var(--muted);
            margin-top: 2px;
        }

        /* ── SECTION HEADING ───────────────────────── */
        .section-heading {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--primary);
            background: #E6F6F0;
            padding: 5px 10px;
            border-radius: 4px;
            margin-bottom: 10px;
            margin-top: 16px;
            border-left: 3px solid var(--primary);
        }

        /* ── DATA GRID ─────────────────────────────── */
        .data-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .data-cell {
            padding: 10px 14px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .data-cell:last-child,
        .data-grid .data-cell:nth-child(2n) {
            border-right: none;
        }

        .data-grid .data-cell:nth-last-child(-n+2) {
            border-bottom: none;
        }

        .data-cell .label {
            font-size: 8pt;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 4px;
        }

        .data-cell .value {
            font-size: 11pt;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.3;
        }

        .data-cell.full {
            grid-column: 1 / -1;
            border-right: none;
        }

        /* ── TANDA TANGAN AREA ─────────────────────── */
        .sign-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin-top: 32px;
        }

        .sign-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 12px 14px;
            text-align: center;
        }

        .sign-box .sign-title {
            font-size: 8.5pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            margin-bottom: 64px; /* ruang TTD */
        }

        .sign-box .sign-name {
            border-top: 1.5px solid var(--ink);
            font-size: 9.5pt;
            font-weight: 700;
            color: var(--ink);
            padding-top: 6px;
        }

        /* ── FOOTER ────────────────────────────────── */
        .doc-footer {
            margin-top: 30px;
            padding-top: 12px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 8pt;
            color: var(--muted);
        }

        /* ── PRINT MEDIA QUERY ─────────────────────── */
        @media screen {
            body { background: #E8EDEB; }
            .tracer-wrap {
                background: #fff;
                margin: 20px auto;
                box-shadow: 0 4px 20px rgba(0,0,0,.12);
                border-radius: 8px;
            }

            .no-print-btn {
                display: flex;
                justify-content: center;
                gap: 12px;
                padding: 20px 0;
                background: #fff;
                border-bottom: 1px solid var(--border);
                margin-bottom: 0;
            }

            .btn-print {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 9px 22px;
                border-radius: 10px;
                font-family: inherit;
                font-size: 13px;
                font-weight: 700;
                cursor: pointer;
                border: none;
                transition: all .2s;
            }

            .btn-print-primary {
                background: var(--primary);
                color: #fff;
            }

            .btn-print-primary:hover { background: var(--primary-dark); }

            .btn-print-secondary {
                background: #F5F8F7;
                color: var(--muted);
                border: 1.5px solid var(--border);
            }

            .btn-print-secondary:hover { background: #E8EDEB; color: var(--ink); }
        }

        @media print {
            .no-print-btn { display: none !important; }
            body { background: #fff; }
            .tracer-wrap { padding: 10mm 12mm; box-shadow: none; }
            @page { size: A4 portrait; margin: 0; }
        }
    </style>
</head>
<body>

    <!-- Tombol print (hanya tampil di layar, tidak ikut cetak) -->
    <div class="no-print-btn">
        <button class="btn-print btn-print-primary" onclick="window.print()">
            🖨️ Cetak Resi
        </button>
        <a href="{{ route('points.index') }}" class="btn-print btn-print-secondary" style="text-decoration:none;">
            ✕ Kembali
        </a>
    </div>

    <div class="tracer-wrap">

        <!-- HEADER -->
        <div class="rs-header">
            <div class="rs-logo-box" aria-hidden="true">
                <svg viewBox="0 0 56 56" xmlns="http://www.w3.org/2000/svg">
                    <rect width="56" height="56" rx="10" fill="#E6F6F0"/>
                    <circle cx="28" cy="14" r="7" fill="#0F7B63"/>
                    <circle cx="28" cy="42" r="7" fill="#0E7490"/>
                    <circle cx="14" cy="28" r="7" fill="#B8912E"/>
                    <circle cx="42" cy="28" r="7" fill="#15966F"/>
                    <circle cx="28" cy="28" r="8" fill="#fff" stroke="#0A5644" stroke-width="1.2"/>
                    <path d="M28 23v10M23 28h10" stroke="#0F7B63" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="rs-header-text">
                <h1>RS Islam Sakinah</h1>
                <p>Sistem Pendaftaran Rawat Jalan<br>My Sakinah Agent</p>
            </div>
            <div class="tracer-title-box">
                <div class="doc-type">Resi Penukaran Poin</div>
                <div class="doc-no">#{{ str_pad($redemption->id, 5, '0', STR_PAD_LEFT) }}</div>
                <div class="doc-date">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        @if(session('success'))
        <div style="background:#ecfdf5; color:#065f46; border:1px solid #a7f3d0; padding:12px 16px; border-radius:6px; margin-bottom:16px; font-size:10pt; text-align:center; font-weight:600;">
            ✅ {{ session('success') }}
        </div>
        @endif

        <!-- INFORMASI PENUKARAN -->
        <div class="section-heading">Detail Penukaran</div>
        <div class="data-grid">
            <div class="data-cell">
                <span class="label">Nama Petugas</span>
                <span class="value">{{ $redemption->user->name }}</span>
            </div>
            <div class="data-cell">
                <span class="label">Tanggal Pengajuan</span>
                <span class="value">{{ $redemption->created_at->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
            <div class="data-cell">
                <span class="label">Jenis Penukaran</span>
                <span class="value" style="color:var(--primary); font-weight:700;">{{ $redemption->type }}</span>
            </div>
            <div class="data-cell">
                <span class="label">Poin Ditukar</span>
                <span class="value" style="color:var(--accent); font-weight:800;">{{ number_format($redemption->points) }} Poin</span>
            </div>
        </div>

        <div style="margin-top: 16px; padding: 12px 16px; background: #FFFBEB; border: 1px solid #FEF3C7; border-radius: 6px; font-size: 9pt; color: #92400E; line-height: 1.5;">
            <strong>ℹ️ Keterangan:</strong> Harap simpan resi ini dan serahkan kepada Admin SDM / Keuangan saat Anda ingin mengambil hadiah atau pencairan uang tunai Anda (setelah status penukaran disetujui).
        </div>

        <!-- TANDA TANGAN -->
        <div class="sign-row">
            <div class="sign-box">
                <div class="sign-title">Petugas Yang Mengajukan</div>
                <div class="sign-name">{{ $redemption->user->name }}</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Admin Pemeriksa</div>
                <div class="sign-name">( _________________________ )</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="doc-footer">
            <span>RSI Sakinah — My Sakinah Agent v1.0</span>
            <span>Ref: RED-{{ date('Ymd') }}-{{ $redemption->id }}</span>
        </div>

    </div>

    @if(session('success'))
    <script>
        // Otomatis buka dialog print saat pertama kali diarahkan (jika ada pesan sukses)
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        }
    </script>
    @endif
</body>
</html>
