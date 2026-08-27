<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Pendaftaran – {{ $registration->kode_booking }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Spectral:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root {
            --primary:      #0F7B63;
            --primary-dark: #0A5644;
            --primary-light:#24977D;
            --primary-soft: #E8F4F0;
            --accent:       #D4AF37;
            --bg:           #F8FAF9;
            --surface:      #FFFFFF;
            --ink:          #1F2D27;
            --muted:        #64766D;
            --border:       #E2E8E5;
            --danger:       #DC2626;
            --danger-soft:  #FEF2F2;
            --warning:      #D97706;
            --warning-soft: #FFFBEB;
            --success:      #059669;
            --success-soft: #ECFDF5;
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--ink);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .public-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            padding: 24px 0;
            color: white;
            text-align: center;
            box-shadow: 0 4px 20px rgba(10,86,68,0.15);
        }
        .brand-logo { width:48px; height:48px; margin-bottom:12px; }
        .main-container {
            flex: 1;
            padding: 32px 20px;
            max-width: 620px;
            margin: 0 auto;
            width: 100%;
        }

        /* ── Cards ── */
        .card-tracer {
            background: var(--surface);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 8px 24px rgba(0,0,0,0.04);
            overflow: hidden;
            margin-bottom: 20px;
        }
        .card-header-tracer {
            background: var(--primary-soft);
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-body-tracer { padding: 28px 24px; }

        /* ── Nomor Antrean Hero ── */
        .nomor-hero {
            text-align: center;
            padding: 20px 0 8px;
        }
        .nomor-antrean {
            font-family: 'Spectral', serif;
            font-size: 5rem;
            font-weight: 800;
            color: var(--primary-dark);
            line-height: 1;
            letter-spacing: 0.05em;
        }
        .nomor-label {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--muted);
            margin-bottom: 8px;
        }

        /* ── Status Badge ── */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.88rem;
            margin: 12px 0;
        }
        .status-menunggu  { background: #FFF7ED; color: #C2410C; border:1px solid #FFEDD5; }
        .status-diperiksa { background: #EFF6FF; color: #1D4ED8; border:1px solid #DBEAFE; }
        .status-selesai   { background: var(--success-soft); color: var(--success); border:1px solid #BBF7D0; }
        .status-batal     { background: var(--danger-soft);  color: var(--danger);  border:1px solid #FEE2E2; }

        /* ── Info Grid ── */
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            text-align: left;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px dashed var(--border);
        }
        .info-item .label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 3px;
        }
        .info-item .value {
            font-size: 0.92rem;
            font-weight: 600;
            color: var(--ink);
        }

        /* ── Alert Boxes ── */
        .alert-box {
            border-radius: 14px;
            padding: 16px 20px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 0.88rem;
            margin-bottom: 16px;
        }
        .alert-box i { font-size: 1.2rem; flex-shrink: 0; margin-top: 1px; }
        .alert-success { background: var(--success-soft); color: #065F46; border: 1px solid #BBF7D0; }
        .alert-warning { background: var(--warning-soft); color: #92400E; border: 1px solid #FDE68A; }
        .alert-danger  { background: var(--danger-soft);  color: #991B1B; border: 1px solid #FECACA; }
        .alert-info    { background: var(--primary-soft); color: var(--primary-dark); border: 1px solid #A7D9CA; }

        /* ── Tombol Ambil Antrean ── */
        .btn-ambil {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            border: none;
            border-radius: 14px;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 14px rgba(15,123,99,0.35);
        }
        .btn-ambil:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(15,123,99,0.45); }
        .btn-ambil:active { transform: translateY(0); }
        .btn-ambil:disabled { opacity: 0.65; cursor: not-allowed; transform: none; }

        /* ── Flow Steps ── */
        .flow-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }
        .flow-steps {
            display: flex;
            justify-content: space-between;
            position: relative;
        }
        .flow-steps::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 30px; right: 30px;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }
        .step { position: relative; z-index: 2; text-align: center; width: 33.33%; }
        .step-icon {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 8px;
            color: var(--muted);
            font-size: 1rem;
            transition: all 0.3s;
        }
        .step.active .step-icon    { background: var(--primary); border-color: var(--primary); color: white; }
        .step.completed .step-icon { background: var(--primary-light); border-color: var(--primary-light); color: white; }
        .step-label { font-size: 0.72rem; font-weight: 600; color: var(--muted); }
        .step.active .step-label, .step.completed .step-label { color: var(--primary-dark); }

        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; gap: 14px; }
            .nomor-antrean { font-size: 4rem; }
        }
    </style>
</head>
<body>

    <header class="public-header">
        <svg class="brand-logo" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <circle cx="20" cy="10" r="6" fill="#0F7B63"/>
            <circle cx="20" cy="30" r="6" fill="#0E7490"/>
            <circle cx="10" cy="20" r="6" fill="#B8912E"/>
            <circle cx="30" cy="20" r="6" fill="#15966F"/>
            <circle cx="20" cy="20" r="6.5" fill="#FFFFFF" stroke="#0A5644" stroke-width="1"/>
            <path d="M20 16.5v7M16.5 20h7" stroke="#0F7B63" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        <h1 class="h5 fw-bold mb-1">My Sakinah Agent</h1>
        <p class="mb-0" style="font-size:0.85rem; opacity:0.9;">Informasi Pendaftaran Rawat Jalan</p>
    </header>

    <div class="main-container">

        {{-- ── Flash messages ── --}}
        @if(session('success'))
            <div class="alert-box alert-success mb-4">
                <i class="bi bi-check-circle-fill"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-box alert-danger mb-4">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>{{ session('error') }}</div>
            </div>
        @endif

        {{-- ── Kartu Utama ── --}}
        <div class="card-tracer">
            {{-- Header card --}}
            <div class="card-header-tracer">
                <div>
                    <div style="font-size:0.7rem; color:var(--muted); font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">Kode Booking</div>
                    <div style="font-weight:800; color:var(--ink); font-size:1.15rem; font-family:monospace; letter-spacing:2px;">
                        {{ $registration->kode_booking }}
                    </div>
                </div>
                <div class="text-end">
                    <div style="font-size:0.7rem; color:var(--muted); font-weight:700; letter-spacing:0.08em; text-transform:uppercase;">Tanggal Kunjungan</div>
                    <div style="font-weight:700; color:var(--primary); font-size:0.95rem;">
                        {{ $registration->tanggal_kunjungan->translatedFormat('d F Y') }}
                    </div>
                </div>
            </div>

            <div class="card-body-tracer">

                {{-- ── Nomor Antrean (jika sudah diambil) ── --}}
                @if($registration->nomor_antrian)
                    <div class="nomor-hero text-center">
                        <div class="nomor-label">Nomor Antrean Anda</div>
                        <div class="nomor-antrean">{{ $registration->nomor_antrian }}</div>
                        <div class="mt-2" style="font-size:0.82rem; color:var(--muted);">
                            {{ $registration->department->nama_poli }}
                        </div>
                    </div>

                    {{-- Status badge --}}
                    @php
                        $statusClass = match($registration->status) {
                            'menunggu'   => 'status-menunggu',
                            'diperiksa'  => 'status-diperiksa',
                            'selesai'    => 'status-selesai',
                            'batal'      => 'status-batal',
                            default      => '',
                        };
                        $statusIcon = match($registration->status) {
                            'menunggu'   => 'bi-hourglass-split',
                            'diperiksa'  => 'bi-person-check-fill',
                            'selesai'    => 'bi-check-circle-fill',
                            'batal'      => 'bi-x-circle-fill',
                            default      => 'bi-circle',
                        };
                    @endphp
                    <div class="text-center">
                        <div class="status-badge {{ $statusClass }}">
                            <i class="bi {{ $statusIcon }}"></i>
                            {{ $registration->status_label }}
                        </div>
                    </div>

                @else
                    {{-- Belum ada nomor antrean --}}
                    <div class="text-center py-2">
                        <div style="width:60px; height:60px; background:var(--primary-soft); color:var(--primary);
                                    border-radius:16px; display:flex; align-items:center; justify-content:center;
                                    font-size:1.8rem; margin:0 auto 12px;">
                            <i class="bi bi-ticket-perforated"></i>
                        </div>
                        <div class="nomor-label">Status Booking</div>
                        <div style="font-size:1.1rem; font-weight:700; color:var(--ink); margin-bottom:4px;">
                            @if($registration->status_booking === 'expired')
                                <span style="color:var(--danger);">Expired</span>
                            @elseif($registration->status_booking === 'cancelled')
                                <span style="color:var(--danger);">Dibatalkan</span>
                            @else
                                <span style="color:var(--warning);">Belum Ambil Antrean</span>
                            @endif
                        </div>
                    </div>

                    {{-- Info window waktu --}}
                    @if($registration->status_booking === 'pending')
                        @php $hari = today()->toDateString(); @endphp
                        @if($registration->tanggal_kunjungan->toDateString() === $hari)
                            @if($windowStatus === 'too_early')
                                <div class="alert-box alert-warning mt-3">
                                    <i class="bi bi-clock-history"></i>
                                    <div>
                                        <strong>Pengambilan antrean belum dibuka.</strong><br>
                                        Silakan kembali mulai pukul <strong>{{ $jamMulaiAmbil }} WIB</strong>.
                                    </div>
                                </div>
                            @elseif($windowStatus === 'expired')
                                <div class="alert-box alert-danger mt-3">
                                    <i class="bi bi-clock-fill"></i>
                                    <div>
                                        <strong>Waktu pengambilan antrean sudah habis.</strong><br>
                                        Jadwal praktik dokter telah berakhir.
                                    </div>
                                </div>
                            @elseif($bisaAmbilAntrean)
                                <div class="alert-box alert-info mt-3">
                                    <i class="bi bi-info-circle-fill"></i>
                                    <div>
                                        <strong>Pengambilan antrean sudah dibuka!</strong><br>
                                        Klik tombol di bawah untuk mendapatkan nomor antrean Anda.
                                    </div>
                                </div>
                            @endif
                        @elseif($registration->tanggal_kunjungan->gt(today()))
                            <div class="alert-box alert-info mt-3">
                                <i class="bi bi-calendar-event"></i>
                                <div>
                                    Kunjungan Anda dijadwalkan pada
                                    <strong>{{ $registration->tanggal_kunjungan->translatedFormat('d F Y') }}</strong>.
                                    Antrean dapat diambil pada hari kunjungan.
                                </div>
                            </div>
                        @endif
                    @elseif($registration->status_booking === 'expired')
                        <div class="alert-box alert-danger mt-3">
                            <i class="bi bi-clock-fill"></i>
                            <div>Kode booking ini sudah <strong>expired</strong> dan tidak dapat digunakan lagi.</div>
                        </div>
                    @endif

                    {{-- Tombol Ambil Antrean --}}
                    @if($bisaAmbilAntrean)
                        <form action="{{ route('public.ambil.antrean') }}" method="POST" class="mt-4" id="form-ambil">
                            @csrf
                            <input type="hidden" name="kode_booking" value="{{ $registration->kode_booking }}">
                            <button type="submit" class="btn-ambil" id="btn-ambil">
                                <i class="bi bi-ticket-perforated me-2"></i>
                                Ambil Nomor Antrean Sekarang
                            </button>
                        </form>
                    @endif
                @endif

                {{-- Info pasien & jadwal --}}
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Nama Pasien</div>
                        <div class="value">{{ $registration->patient->nama_pasien }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Poli Tujuan</div>
                        <div class="value">{{ $registration->department->nama_poli }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Dokter</div>
                        <div class="value">{{ $registration->doctor->nama_dokter }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Jam Praktik</div>
                        <div class="value">
                            {{ substr($registration->doctorSchedule->jam_mulai, 0, 5) }} –
                            {{ substr($registration->doctorSchedule->jam_selesai, 0, 5) }} WIB
                        </div>
                    </div>
                </div>

            </div>{{-- end card-body --}}
        </div>

        {{-- ── Alur Pelayanan ── --}}
        @if($registration->nomor_antrian)
        <div class="flow-container mb-4">
            <div style="font-size:0.85rem; font-weight:700; color:var(--primary-dark); text-align:center; margin-bottom:20px;">
                Alur Pelayanan Pasien
            </div>
            <div class="flow-steps">
                @php
                    $step1 = $step2 = $step3 = '';
                    if ($registration->status === 'menunggu')  { $step1 = 'active'; }
                    elseif ($registration->status === 'diperiksa') { $step1 = 'completed'; $step2 = 'active'; }
                    elseif ($registration->status === 'selesai')   { $step1 = 'completed'; $step2 = 'completed'; $step3 = 'active'; }
                @endphp
                <div class="step {{ $step1 }}">
                    <div class="step-icon"><i class="bi bi-clock"></i></div>
                    <div class="step-label">Menunggu</div>
                </div>
                <div class="step {{ $step2 }}">
                    <div class="step-icon"><i class="bi bi-person-check"></i></div>
                    <div class="step-label">Diperiksa</div>
                </div>
                <div class="step {{ $step3 }}">
                    <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>
            <div class="text-center mt-4">
                <p style="font-size:0.8rem; color:var(--muted); margin-bottom:0;">
                    <i class="bi bi-info-circle me-1"></i>
                    Silakan tunjukkan halaman ini kepada petugas saat dipanggil.
                </p>
            </div>
        </div>
        @endif

        {{-- Link kembali --}}
        <div class="text-center">
            <a href="{{ route('public.cek') }}" style="color:var(--primary); font-size:0.88rem; text-decoration:none;">
                <i class="bi bi-arrow-left me-1"></i>Cari kode booking lain
            </a>
        </div>

    </div>

    <script>
        // Loading state saat submit ambil antrean
        const formAmbil = document.getElementById('form-ambil');
        if (formAmbil) {
            formAmbil.addEventListener('submit', function() {
                const btn = document.getElementById('btn-ambil');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Memproses...';
            });
        }
    </script>

</body>
</html>
