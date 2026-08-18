<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Antrian - {{ $registration->nomor_antrian }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&display=swap');
        
        body {
            font-family: 'Space Mono', monospace;
            width: 80mm; /* Lebar standar printer thermal */
            margin: 0 auto;
            padding: 15px;
            color: #000;
            background: #f8fafc;
        }
        
        .ticket {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid #e2e8f0;
        }

        .text-center { text-align: center; }
        .fw-bold { font-weight: bold; }
        
        .header { margin-bottom: 15px; border-bottom: 2px dashed #cbd5e1; padding-bottom: 15px; }
        .title { font-size: 1.4rem; letter-spacing: -0.5px; margin-bottom: 4px; }
        .subtitle { font-size: 0.8rem; color: #475569; }
        
        .antrian-section { margin: 20px 0; }
        .antrian-label { font-size: 0.85rem; color: #64748b; margin-bottom: 5px; letter-spacing: 1px; }
        .nomor { font-size: 3.5rem; font-weight: 700; line-height: 1; margin: 0; color: #0f172a; }
        
        .info-grid { 
            display: grid; 
            grid-template-columns: 1fr; 
            gap: 10px; 
            text-align: left; 
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #cbd5e1;
        }
        .info-item { display: flex; flex-direction: column; }
        .info-label { font-size: 0.7rem; color: #64748b; text-transform: uppercase; }
        .info-value { font-size: 0.9rem; font-weight: bold; color: #1e293b; }
        
        .footer { 
            font-size: 0.75rem; 
            text-align: center; 
            border-top: 2px solid #e2e8f0; 
            padding-top: 15px; 
            margin-top: 20px; 
            color: #475569;
        }
        
        /* Hilangkan styling khusus layar saat benar-benar dicetak */
        @media print {
            body { width: 100%; margin: 0; padding: 0; background: #fff; }
            .ticket { box-shadow: none; border: none; padding: 0; margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="ticket text-center">
        <div class="header">
            <div class="fw-bold title">RS ISLAM SAKINAH</div>
            <div class="subtitle">Tiket Pendaftaran Pasien</div>
        </div>
        
        <div class="antrian-section">
            <div class="antrian-label">NOMOR ANTRIAN</div>
            <div class="nomor">{{ $registration->nomor_antrian }}</div>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Pasien</span>
                <span class="info-value">{{ $registration->patient->nama_pasien }} ({{ $registration->patient->no_rm }})</span>
            </div>
            <div class="info-item">
                <span class="info-label">Poli Tujuan</span>
                <span class="info-value">{{ $registration->department->nama_poli }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Dokter</span>
                <span class="info-value">{{ $registration->doctor->nama_dokter }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Tanggal Kunjungan</span>
                <span class="info-value">{{ $registration->tanggal_daftar->translatedFormat('d F Y') }}</span>
            </div>
        </div>
        
        <div class="footer">
            Harap menunggu di ruang tunggu poli.<br>
            Semoga lekas sembuh!
        </div>
    </div>
</body>
</html>
