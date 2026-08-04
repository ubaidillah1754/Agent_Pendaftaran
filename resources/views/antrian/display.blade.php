<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Antrian {{ $department->nama_poli }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Amiri:wght@400;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --rs-green-dark: #052E22;
            --rs-green:      #0B6B4F;
            --rs-gold:       #C9A227;
            --rs-gold-light: #E8C766;
            --rs-tile:       #0E7490;
        }
        * { box-sizing: border-box; }
        body {
            font-family:'Plus Jakarta Sans',sans-serif;
            background: linear-gradient(160deg, var(--rs-green-dark) 0%, #063D2C 55%, var(--rs-green-dark) 100%);
            color:#fff;min-height:100vh;margin:0;display:flex;flex-direction:column;
            position: relative; overflow: hidden;
        }
        body::before {
            content: ""; position: absolute; inset: 0; z-index: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='72' height='72'%3E%3Cg fill='none' stroke='%23E8C766' stroke-width='1' opacity='0.06'%3E%3Crect x='18' y='18' width='36' height='36' transform='rotate(45 36 36)'/%3E%3Crect x='18' y='18' width='36' height='36'/%3E%3C/g%3E%3C/svg%3E");
        }
        .header, .main, .footer { position: relative; z-index: 1; }

        .header {
            padding:18px 40px;border-bottom:1px solid rgba(232,199,102,.18);
            display:flex;align-items:center;justify-content:space-between;
        }
        .header .brand { display:flex; align-items:center; gap:14px; }
        .header .logo-badge {
            width:52px;height:52px;border-radius:50%;background:#fff;
            display:flex;align-items:center;justify-content:center;padding:5px;
            border:2px solid var(--rs-gold-light); flex-shrink:0;
        }
        .header .logo-badge img { width:100%;height:100%;object-fit:contain; }
        .header h1 { margin:0;font-family:'Amiri',serif;font-size:1.35rem;font-weight:700; }
        .header .poli { font-size:.85rem;color:rgba(255,255,255,.55); }
        .clock { font-size:2rem;font-weight:900;color:var(--rs-gold-light); }

        .main { flex:1;display:grid;grid-template-columns:1fr 1fr;gap:0; }
        .panel-left { padding:40px;border-right:1px solid rgba(232,199,102,.14); }
        .panel-right { padding:40px; }
        .label { font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.15em;color:rgba(255,255,255,.45);margin-bottom:10px; }
        .current-number {
            font-size:10rem;font-weight:900;line-height:1;color:var(--rs-gold-light);
            text-shadow:0 0 60px rgba(201,162,39,.45);
        }
        .current-name { font-size:1.8rem;font-weight:700;margin-top:8px; }
        .current-meta { font-size:.9rem;color:rgba(255,255,255,.5);margin-top:4px; }
        .next-title { font-size:1rem;font-weight:700;margin-bottom:20px;color:rgba(255,255,255,.75); }
        .next-item {
            background:rgba(255,255,255,.06);border:1px solid rgba(232,199,102,.12);
            border-radius:16px;padding:16px 20px;margin-bottom:10px;
            display:flex;align-items:center;gap:16px;
        }
        .next-no {
            font-size:1.4rem;font-weight:900;color:var(--rs-gold-light);min-width:60px;
            width:60px;height:52px;border-radius:50% 50% 10px 10px;
            background:rgba(232,199,102,.12);
            display:flex;align-items:center;justify-content:center;
        }
        .next-name { font-size:.95rem;font-weight:600; }
        .no-antrian { text-align:center;color:rgba(255,255,255,.35);margin-top:60px; }
        .footer {
            padding:12px 40px;border-top:1px solid rgba(232,199,102,.14);
            display:flex;justify-content:space-between;align-items:center;
            font-size:.75rem;color:rgba(255,255,255,.35);
        }
        @keyframes blink { 0%,100%{opacity:1}50%{opacity:.4} }
        .blink { animation:blink 1.5s infinite; }
    </style>
</head>
<body>
<div class="header">
    <div class="brand">
        <div class="logo-badge">
            <img src="https://i.ibb.co.com/wmvGtC3/logo-sakinah.png"  alt="Logo RS Islam Sakinah">
        </div>
        <div>
            <h1>Antrian Poli</h1>
            <div class="poli">{{ $department->nama_poli }} — RS Islam "Sakinah"</div>
        </div>
    </div>
    <div class="text-end">
        <div class="clock" id="clock">--:--:--</div>
        <div style="font-size:.75rem;color:rgba(255,255,255,.4);" id="date-str"></div>
    </div>
</div>

<div class="main">
    <!-- Sedang Dipanggil -->
    <div class="panel-left">
        <div class="label"><i class="bi bi-megaphone me-1"></i>Sedang Dipanggil</div>
        @if($sedangDipanggil)
        <div class="current-number blink">{{ $sedangDipanggil->nomor_antrian }}</div>
        <div class="current-name">{{ $sedangDipanggil->patient->nama_pasien }}</div>
        <div class="current-meta">Silakan menuju ruang periksa</div>
        @else
        <div class="no-antrian">
            <i class="bi bi-hourglass" style="font-size:4rem;display:block;margin-bottom:12px;"></i>
            <p>Belum ada pasien dipanggil</p>
        </div>
        @endif
    </div>

    <!-- Antrian Berikutnya -->
    <div class="panel-right">
        <div class="next-title"><i class="bi bi-list-ol me-2" style="color:var(--rs-gold-light);"></i>Antrian Berikutnya</div>
        @forelse($menunggu as $reg)
        <div class="next-item">
            <div class="next-no">{{ $reg->nomor_antrian }}</div>
            <div class="next-name">{{ $reg->patient->nama_pasien }}</div>
        </div>
        @empty
        <div style="color:rgba(255,255,255,.35);text-align:center;margin-top:40px;">
            <i class="bi bi-check-circle" style="font-size:3rem;display:block;margin-bottom:8px;"></i>
            <p>Tidak ada antrian menunggu</p>
        </div>
        @endforelse
    </div>
</div>

<div class="footer">
    <span>Sistem Pendaftaran Rawat Jalan — RS Islam "Sakinah" Mojokerto</span>
    <span>Halaman ini refresh otomatis setiap 15 detik</span>
</div>

<script>
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent = now.toLocaleTimeString('id-ID');
    document.getElementById('date-str').textContent = now.toLocaleDateString('id-ID',{weekday:'long',day:'numeric',month:'long',year:'numeric'});
}
setInterval(updateClock, 1000); updateClock();
// Auto refresh halaman setiap 15 detik untuk data terbaru
setInterval(() => window.location.reload(), 15000);
</script>
</body>
</html>