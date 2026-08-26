<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracer Pendaftaran - {{ $registration->kode_booking }}</title>
    <!-- Google Fonts: Inter & Spectral -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Spectral:wght@500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0F7B63;
            --primary-dark: #0A5644;
            --primary-light: #24977D;
            --primary-soft: #E8F4F0;
            --accent: #D4AF37;
            --bg: #F8FAF9;
            --surface: #FFFFFF;
            --ink: #1F2D27;
            --muted: #64766D;
            --border: #E2E8E5;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
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
            box-shadow: 0 4px 20px rgba(10, 86, 68, 0.15);
        }
        .brand-logo {
            width: 48px;
            height: 48px;
            margin-bottom: 12px;
        }
        .main-container {
            flex: 1;
            padding: 40px 20px;
            max-width: 600px;
            margin: 0 auto;
            width: 100%;
        }
        .card-tracer {
            background: var(--surface);
            border-radius: 20px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            overflow: hidden;
            margin-bottom: 30px;
        }
        .card-tracer-header {
            background: var(--primary-soft);
            padding: 20px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card-tracer-body {
            padding: 32px 24px;
            text-align: center;
        }
        .nomor-antrean {
            font-family: 'Spectral', serif;
            font-size: 4rem;
            font-weight: 700;
            color: var(--primary-dark);
            line-height: 1.1;
            margin: 16px 0;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }
        .status-menunggu { background: #FFF7ED; color: #C2410C; border: 1px solid #FFEDD5; }
        .status-dipanggil { background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; }
        .status-selesai { background: #ECFDF5; color: #047857; border: 1px solid #D1FAE5; }
        .status-batal { background: #FEF2F2; color: #B91C1C; border: 1px solid #FEE2E2; }
        
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            text-align: left;
            margin-top: 30px;
            padding-top: 24px;
            border-top: 1px dashed var(--border);
        }
        .info-item .label {
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            font-weight: 600;
            margin-bottom: 4px;
        }
        .info-item .value {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--ink);
        }
        
        /* Flow / Progress */
        .flow-container {
            background: white;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid var(--border);
        }
        .flow-title {
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--primary-dark);
            margin-bottom: 20px;
            text-align: center;
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
            left: 30px;
            right: 30px;
            height: 2px;
            background: var(--border);
            z-index: 1;
        }
        .step {
            position: relative;
            z-index: 2;
            text-align: center;
            width: 33.33%;
        }
        .step-icon {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: white;
            border: 2px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 8px;
            color: var(--muted);
            font-size: 1rem;
            transition: all 0.3s;
        }
        .step.active .step-icon {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        .step.completed .step-icon {
            background: var(--primary-light);
            border-color: var(--primary-light);
            color: white;
        }
        .step-label {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--muted);
        }
        .step.active .step-label {
            color: var(--primary-dark);
        }
        
        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; gap: 16px; }
            .nomor-antrean { font-size: 3.5rem; }
            .flow-steps::before { left: 20px; right: 20px; }
        }
    </style>
</head>
<body>

    <header class="public-header">
        <svg class="brand-logo" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <circle cx="20" cy="10" r="6" fill="#0F7B63" />
            <circle cx="20" cy="30" r="6" fill="#0E7490" />
            <circle cx="10" cy="20" r="6" fill="#B8912E" />
            <circle cx="30" cy="20" r="6" fill="#15966F" />
            <circle cx="20" cy="20" r="6.5" fill="#FFFFFF" stroke="#0A5644" stroke-width="1" />
            <path d="M20 16.5v7M16.5 20h7" stroke="#0F7B63" stroke-width="1.6" stroke-linecap="round" />
        </svg>
        <h1 class="h5 fw-bold mb-1">My Sakinah Agent</h1>
        <p class="mb-0" style="font-size: 0.85rem; opacity: 0.9;">Informasi Pendaftaran Rawat Jalan</p>
    </header>

    <div class="main-container">
        
        <div class="card-tracer">
            <div class="card-tracer-header">
                <div>
                    <div style="font-size:0.75rem; color:var(--muted); font-weight:600;">KODE BOOKING</div>
                    <div style="font-weight:700; color:var(--ink); font-size:1.1rem;">{{ $registration->kode_booking }}</div>
                </div>
                <div class="text-end">
                    <div style="font-size:0.75rem; color:var(--muted); font-weight:600;">TANGGAL PERIKSA</div>
                    <div style="font-weight:700; color:var(--primary); font-size:0.95rem;">{{ \Carbon\Carbon::parse($registration->tanggal_daftar)->translatedFormat('d F Y') }}</div>
                </div>
            </div>
            
            <div class="card-tracer-body">
                <div style="font-size:0.85rem; color:var(--muted); font-weight:600; text-transform:uppercase; letter-spacing:0.05em;">Nomor Antrean Anda</div>
                <div class="nomor-antrean">{{ $registration->nomor_antrian }}</div>
                
                @php
                    $statusClass = '';
                    $statusIcon = '';
                    switch($registration->status) {
                        case 'menunggu': $statusClass = 'status-menunggu'; $statusIcon = 'bi-hourglass-split'; break;
                        case 'dipanggil': $statusClass = 'status-dipanggil'; $statusIcon = 'bi-megaphone-fill'; break;
                        case 'selesai': $statusClass = 'status-selesai'; $statusIcon = 'bi-check-circle-fill'; break;
                        case 'batal': $statusClass = 'status-batal'; $statusIcon = 'bi-x-circle-fill'; break;
                    }
                @endphp
                <div class="status-badge {{ $statusClass }}">
                    <i class="bi {{ $statusIcon }}"></i>
                    {{ ucfirst($registration->status) }}
                </div>
                
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
                        <div class="label">Waktu Praktek</div>
                        <div class="value">{{ substr($registration->doctorSchedule->jam_mulai, 0, 5) }} - {{ substr($registration->doctorSchedule->jam_selesai, 0, 5) }} WIB</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flow-container">
            <div class="flow-title">Alur Pelayanan Pasien</div>
            <div class="flow-steps">
                @php
                    $step1 = 'active';
                    $step2 = '';
                    $step3 = '';
                    
                    if ($registration->status == 'dipanggil') {
                        $step1 = 'completed';
                        $step2 = 'active';
                    } elseif ($registration->status == 'selesai') {
                        $step1 = 'completed';
                        $step2 = 'completed';
                        $step3 = 'active';
                    } elseif ($registration->status == 'batal') {
                        $step1 = '';
                    }
                @endphp
                
                <div class="step {{ $step1 }}">
                    <div class="step-icon"><i class="bi bi-clock"></i></div>
                    <div class="step-label">Menunggu</div>
                </div>
                <div class="step {{ $step2 }}">
                    <div class="step-icon"><i class="bi bi-megaphone"></i></div>
                    <div class="step-label">Dipanggil</div>
                </div>
                <div class="step {{ $step3 }}">
                    <div class="step-icon"><i class="bi bi-check-lg"></i></div>
                    <div class="step-label">Selesai</div>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p style="font-size:0.8rem; color:var(--muted); margin-bottom:0;">
                    <i class="bi bi-info-circle me-1"></i> Silakan tunjukkan halaman ini atau Tracer fisik Anda kepada petugas saat dipanggil.
                </p>
            </div>
        </div>
        
    </div>

</body>
</html>
