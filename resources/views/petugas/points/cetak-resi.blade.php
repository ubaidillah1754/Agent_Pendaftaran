<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resi Penukaran Reward — {{ $redemption->reference_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Amiri:wght@700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            padding: 40px 20px;
            font-size: 14px;
        }
        .ticket-wrapper {
            max-width: 480px;
            margin: 0 auto;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e2e8f0;
            overflow: hidden;
        }
        .ticket-header {
            background: #0F7B63;
            color: #fff;
            padding: 24px 20px;
            text-align: center;
        }
        .ticket-header h1 {
            font-family: 'Amiri', serif;
            font-size: 1.6rem;
            margin-bottom: 4px;
        }
        .ticket-header p {
            font-size: 0.78rem;
            opacity: 0.85;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }
        .ticket-body {
            padding: 24px;
        }
        .ref-box {
            background: #f1f5f9;
            border-radius: 10px;
            padding: 12px;
            text-align: center;
            margin-bottom: 20px;
        }
        .ref-box .label {
            font-size: 0.7rem;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .ref-box .code {
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f172a;
            font-family: monospace;
            letter-spacing: 0.05em;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 8px 0;
            border-bottom: 1px dashed #e2e8f0;
        }
        .info-table td.label {
            color: #64748b;
            width: 40%;
        }
        .info-table td.value {
            font-weight: 600;
            text-align: right;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 700;
            background: #fef3c7;
            color: #92400e;
        }
        .ticket-footer {
            padding: 16px 24px;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            font-size: 0.75rem;
            color: #64748b;
        }
        .no-print {
            text-align: center;
            margin-top: 20px;
        }
        .btn-print {
            background: #0F7B63;
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .ticket-wrapper { box-shadow: none; border: 1px solid #ccc; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="ticket-wrapper">
    <div class="ticket-header">
        <h1>My Sakinah Agent</h1>
        <p>Bukti Penukaran Poin &amp; Reward</p>
    </div>

    <div class="ticket-body">
        <div class="ref-box">
            <div class="label">Nomor Referensi</div>
            <div class="code">{{ $redemption->reference_code }}</div>
        </div>

        <table class="info-table">
            <tr>
                <td class="label">Nama Pemohon</td>
                <td class="value">{{ $redemption->user->name }}</td>
            </tr>
            <tr>
                <td class="label">Reward Ditukar</td>
                <td class="value">{{ $redemption->merchandise_name }}</td>
            </tr>
            <tr>
                <td class="label">Jumlah</td>
                <td class="value">{{ $redemption->quantity }} unit</td>
            </tr>
            <tr>
                <td class="label">Total Poin Dipotong</td>
                <td class="value" style="color:#B8912E;">{{ number_format($redemption->total_points) }} Poin</td>
            </tr>
            <tr>
                <td class="label">Tanggal Pengajuan</td>
                <td class="value">{{ $redemption->created_at->format('d M Y, H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Status Saat Ini</td>
                <td class="value">
                    <span class="status-badge">{{ $redemption->status_label }}</span>
                </td>
            </tr>
            @if($redemption->approver)
            <tr>
                <td class="label">Diproses Oleh</td>
                <td class="value">{{ $redemption->approver->name }}</td>
            </tr>
            @endif
            @if($redemption->notes)
            <tr>
                <td class="label">Catatan</td>
                <td class="value">{{ $redemption->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="ticket-footer">
        <p>Simpan bukti ini sebagai tanda terima pengambilan merchandise resmi di bagian Administrasi.</p>
        <p style="margin-top:4px;">Dicetak otomatis pada {{ now()->format('d M Y, H:i') }}</p>
    </div>
</div>

<div class="no-print">
    <button class="btn-print" onclick="window.print()">Cetak Resi Ini</button>
</div>

</body>
</html>
