<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Halaman utama dashboard dengan ringkasan statistik */
    public function index()
    {
        $today = today()->format('Y-m-d');

        // ── Statistik Utama ────────────────────────────────────────────────
        $stats = [
            'total_pendaftaran_hari_ini' => Registration::whereDate('tanggal_daftar', $today)->count(),
            'menunggu'   => Registration::whereDate('tanggal_daftar', $today)->where('status', 'menunggu')->count(),
            'dipanggil'  => Registration::whereDate('tanggal_daftar', $today)->where('status', 'dipanggil')->count(),
            'selesai'    => Registration::whereDate('tanggal_daftar', $today)->where('status', 'selesai')->count(),
            'batal'      => Registration::whereDate('tanggal_daftar', $today)->where('status', 'batal')->count(),
            'terjadwal'  => Registration::whereDate('tanggal_daftar', '>', $today)->where('status', 'menunggu')->count(),
            'total_pasien'   => Patient::count(),
            'total_dokter'   => Doctor::where('is_active', true)->count(),
            'total_poli'     => Department::where('is_active', true)->count(),
        ];

        // ── Pendaftaran Per Poli Hari Ini ──────────────────────────────────
        $pendaftaranPerPoli = Registration::with('department')
            ->whereDate('tanggal_daftar', $today)
            ->select('department_id', DB::raw('count(*) as total'))
            ->groupBy('department_id')
            ->get()
            ->map(fn($r) => [
                'nama_poli' => $r->department->nama_poli ?? '-',
                'total'     => $r->total,
            ]);

        // ── Grafik Pendaftaran 7 Hari Terakhir ────────────────────────────
        $pendaftaran7Hari = Registration::select(
                DB::raw('DATE(tanggal_daftar) as tanggal'),
                DB::raw('count(*) as total')
            )
            ->where('tanggal_daftar', '>=', now()->subDays(6)->format('Y-m-d'))
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        // Isi tanggal yang tidak ada data dengan 0
        $labels7Hari = [];
        $data7Hari   = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->format('Y-m-d');
            $labels7Hari[] = now()->subDays($i)->format('d M');
            $data7Hari[]   = $pendaftaran7Hari->get($tgl)?->total ?? 0;
        }

        // ── Antrian Terbaru Hari Ini ──────────────────────────────────────
        $antrianTerbaru = Registration::with(['patient', 'department', 'doctor'])
            ->whereDate('tanggal_daftar', $today)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->orderBy('urutan_antrian')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'stats',
            'pendaftaranPerPoli',
            'labels7Hari',
            'data7Hari',
            'antrianTerbaru'
        ));
    }
}
