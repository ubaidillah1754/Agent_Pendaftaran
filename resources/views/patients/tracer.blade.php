<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Pasien — {{ $patient->no_rm }}</title>
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

        .data-grid.three-col {
            grid-template-columns: 1fr 1fr 1fr;
        }

        .data-cell {
            padding: 8px 12px;
            border-right: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .data-cell:last-child,
        .data-grid .data-cell:nth-child(2n) {
            border-right: none;
        }

        .data-grid.three-col .data-cell:nth-child(2n) {
            border-right: 1px solid var(--border);
        }

        .data-grid.three-col .data-cell:nth-child(3n) {
            border-right: none;
        }

        .data-cell .label {
            font-size: 7.5pt;
            font-weight: 700;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: block;
            margin-bottom: 2px;
        }

        .data-cell .value {
            font-size: 10pt;
            font-weight: 600;
            color: var(--ink);
            line-height: 1.3;
        }

        .data-cell .value.empty {
            color: #b0bbb6;
            font-weight: 400;
            font-style: italic;
        }

        /* ── FULL WIDTH CELL ───────────────────────── */
        .data-cell.full {
            grid-column: 1 / -1;
            border-right: none;
        }

        /* ── NO RM BADGE ───────────────────────────── */
        .nrm-badge {
            display: inline-block;
            background: var(--primary);
            color: #fff;
            padding: 4px 14px;
            border-radius: 6px;
            font-size: 12pt;
            font-weight: 800;
            letter-spacing: 1px;
        }

        /* ── PEMBAYARAN BADGE ──────────────────────── */
        .pay-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 8.5pt;
            font-weight: 700;
        }

        .pay-umum { background: #E6F6F0; color: var(--primary-dark); }
        .pay-bpjs { background: #EEF2FF; color: #3730A3; }
        .pay-asuransi { background: #FBF6E9; color: #7A5E17; }

        /* ── RIWAYAT KUNJUNGAN ─────────────────────── */
        .visit-row {
            display: flex;
            gap: 10px;
            align-items: flex-start;
            padding: 8px 0;
            border-bottom: 1px dashed var(--border);
            font-size: 9pt;
        }

        .visit-row:last-child { border-bottom: none; }

        .visit-no {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            background: var(--primary);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: 700;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .visit-body { flex: 1; }

        .visit-body .v-title {
            font-weight: 700;
            color: var(--ink);
        }

        .visit-body .v-sub {
            color: var(--muted);
            font-size: 8.5pt;
            margin-top: 1px;
        }

        .visit-status {
            font-size: 7.5pt;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 999px;
        }

        .vs-selesai { background: #D1FAE5; color: #065F46; }
        .vs-menunggu { background: #FEF3C7; color: #92400E; }
        .vs-batal { background: #FEE2E2; color: #991B1B; }
        .vs-dipanggil { background: #DBEAFE; color: #1E40AF; }

        /* ── TANDA TANGAN AREA ─────────────────────── */
        .sign-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-top: 24px;
        }

        .sign-box {
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }

        .sign-box .sign-title {
            font-size: 8pt;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--muted);
            margin-bottom: 48px; /* ruang TTD */
        }

        .sign-box .sign-name {
            border-top: 1.5px solid var(--ink);
            font-size: 8.5pt;
            font-weight: 600;
            color: var(--ink);
            padding-top: 6px;
        }

        /* ── FOOTER ────────────────────────────────── */
        .doc-footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 7.5pt;
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
            🖨️ Cetak Tracer
        </button>
        <button class="btn-print btn-print-secondary" onclick="window.close()">
            ✕ Tutup
        </button>
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
                <div class="doc-type">Tracer Pasien</div>
                <div class="doc-no">{{ $patient->no_rm }}</div>
                <div class="doc-date">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            </div>
        </div>

        <!-- IDENTITAS PASIEN -->
        <div class="section-heading">Identitas Pasien</div>
        <div class="data-grid">
            <div class="data-cell full">
                <span class="label">Nomor Rekam Medis</span>
                <span class="value"><span class="nrm-badge">{{ $patient->no_rm }}</span></span>
            </div>
            <div class="data-cell full">
                <span class="label">Nama Lengkap Pasien</span>
                <span class="value">{{ $patient->nama_pasien }}</span>
            </div>
            <div class="data-cell">
                <span class="label">NIK</span>
                <span class="value">{{ $patient->nik }}</span>
            </div>
            <div class="data-cell">
                <span class="label">Jenis Kelamin</span>
                <span class="value">{{ $patient->jenis_kelamin_label }}</span>
            </div>
            <div class="data-cell">
                <span class="label">Tempat Lahir</span>
                <span class="value {{ !$patient->tempat_lahir ? 'empty' : '' }}">
                    {{ $patient->tempat_lahir ?: '— tidak diisi —' }}
                </span>
            </div>
            <div class="data-cell">
                <span class="label">Tanggal Lahir / Umur</span>
                <span class="value">
                    {{ $patient->tanggal_lahir->translatedFormat('d F Y') }}
                    ({{ $patient->umur }} tahun)
                </span>
            </div>
            <div class="data-cell full">
                <span class="label">Alamat</span>
                <span class="value">{{ $patient->alamat }}</span>
            </div>
            <div class="data-cell">
                <span class="label">No. Telepon Pasien</span>
                <span class="value {{ !$patient->no_telepon ? 'empty' : '' }}">
                    {{ $patient->no_telepon ?: '— tidak diisi —' }}
                </span>
            </div>
            <div class="data-cell">
                <span class="label">Golongan Darah</span>
                <span class="value">{{ $patient->golongan_darah }}</span>
            </div>
        </div>

        <!-- DATA WALI (jika ada) -->
        @if($patient->nama_wali)
        <div class="section-heading">Data Wali / Penanggung Jawab</div>
        <div class="data-grid">
            <div class="data-cell">
                <span class="label">Nama Wali</span>
                <span class="value">{{ $patient->nama_wali }}</span>
            </div>
            <div class="data-cell">
                <span class="label">No. Telepon Wali</span>
                <span class="value {{ !$patient->no_telepon_wali ? 'empty' : '' }}">
                    {{ $patient->no_telepon_wali ?: '— tidak diisi —' }}
                </span>
            </div>
        </div>
        @endif

        <!-- INFORMASI PEMBAYARAN -->
        <div class="section-heading">Informasi Pembayaran</div>
        <div class="data-grid">
            <div class="data-cell">
                <span class="label">Jenis Pembayaran</span>
                <span class="value">
                    @php
                        $payClass = match($patient->jenis_pembayaran) {
                            'bpjs'     => 'pay-bpjs',
                            'asuransi' => 'pay-asuransi',
                            default    => 'pay-umum',
                        };
                    @endphp
                    <span class="pay-badge {{ $payClass }}">{{ $patient->jenis_pembayaran_label }}</span>
                </span>
            </div>
            @if($patient->jenis_pembayaran === 'bpjs')
            <div class="data-cell">
                <span class="label">Nomor BPJS</span>
                <span class="value">{{ $patient->no_bpjs ?: '— tidak diisi —' }}</span>
            </div>
            @elseif($patient->jenis_pembayaran === 'asuransi')
            <div class="data-cell">
                <span class="label">Nomor Asuransi</span>
                <span class="value">{{ $patient->no_asuransi ?: '— tidak diisi —' }}</span>
            </div>
            @else
            <div class="data-cell">
                <span class="label">Keterangan</span>
                <span class="value empty">Pasien umum (bayar mandiri)</span>
            </div>
            @endif
        </div>

        <!-- RIWAYAT KUNJUNGAN TERAKHIR -->
        @if($patient->registrations->isNotEmpty())
        <div class="section-heading">Kunjungan Terakhir</div>
        @foreach($patient->registrations as $reg)
        <div class="visit-row">
            <div class="visit-no">{{ $loop->iteration }}</div>
            <div class="visit-body">
                <div class="v-title">
                    {{ $reg->department->nama_poli ?? '-' }} —
                    dr. {{ $reg->doctor->nama_dokter ?? '-' }}
                </div>
                <div class="v-sub">
                    Tanggal: {{ $reg->tanggal_daftar->translatedFormat('d F Y') }} ·
                    Antrian: {{ $reg->nomor_antrian }}
                    @if($reg->keluhan)
                        · Keluhan: {{ Str::limit($reg->keluhan, 60) }}
                    @endif
                </div>
            </div>
            @php
                $vsClass = match($reg->status) {
                    'selesai'   => 'vs-selesai',
                    'menunggu'  => 'vs-menunggu',
                    'dipanggil' => 'vs-dipanggil',
                    'batal'     => 'vs-batal',
                    default     => '',
                };
            @endphp
            <span class="visit-status {{ $vsClass }}">{{ $reg->status_label }}</span>
        </div>
        @endforeach
        @endif

        <!-- TANDA TANGAN -->
        <div class="sign-row">
            <div class="sign-box">
                <div class="sign-title">Petugas Pendaftaran</div>
                <div class="sign-name">( _________________________ )</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Mengetahui</div>
                <div class="sign-name">( _________________________ )</div>
            </div>
            <div class="sign-box">
                <div class="sign-title">Pasien / Wali</div>
                <div class="sign-name">( _________________________ )</div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="doc-footer">
            <span>RSI Sakinah — My Sakinah Agent v1.0</span>
            <span>No. RM: <strong>{{ $patient->no_rm }}</strong> — Terdaftar: {{ $patient->created_at->translatedFormat('d F Y') }}</span>
        </div>

    </div>

</body>
</html>
