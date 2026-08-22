<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Registration;
use App\Models\User;
use App\Models\PetugasPoint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Halaman utama dashboard dengan ringkasan statistik */
    public function index(Request $request)
    {
        $isAdmin = auth()->user()->isAdmin();

        // ── Filter Admin ─────────────────────────────────────────────
        $dari = $isAdmin && $request->filled('dari')
            ? $request->dari
            : null;

        $sampai = $isAdmin && $request->filled('sampai')
            ? $request->sampai
            : null;

        $deptId = $isAdmin && $request->filled('department_id')
            ? $request->department_id
            : null;

        $doctorId = $isAdmin && $request->filled('doctor_id')
            ? $request->doctor_id
            : null;

        $userId = $isAdmin && $request->filled('user_id')
            ? $request->user_id
            : null;

        // ── Tanggal hari ini ─────────────────────────────────────────
        $today = today()->format('Y-m-d');

        // Apakah ada filter tanggal aktif?
        $hasDateFilter = $dari || $sampai;

        // ── Base Query Pendaftaran ───────────────────────────────────
        $baseQuery = function () use (
            $dari,
            $sampai,
            $deptId,
            $doctorId,
            $userId,
            $today,
            $hasDateFilter
        ) {
            $q = Registration::query();

            if ($hasDateFilter) {
                if ($dari) {
                    $q->whereDate('tanggal_daftar', '>=', $dari);
                }

                if ($sampai) {
                    $q->whereDate('tanggal_daftar', '<=', $sampai);
                }
            } else {
                $q->whereDate('tanggal_daftar', $today);
            }

            if ($deptId) {
                $q->where('department_id', $deptId);
            }

            if ($doctorId) {
                $q->where('doctor_id', $doctorId);
            }

            if ($userId) {
                $q->where('created_by', $userId);
            }

            return $q;
        };

        // ── Statistik Utama ──────────────────────────────────────────
        $stats = [
            'total_pendaftaran_hari_ini' => (clone $baseQuery())->count(),

            'menunggu' => (clone $baseQuery())
                ->where('status', 'menunggu')
                ->count(),

            'dipanggil' => (clone $baseQuery())
                ->where('status', 'dipanggil')
                ->count(),

            'selesai' => (clone $baseQuery())
                ->where('status', 'selesai')
                ->count(),

            'batal' => (clone $baseQuery())
                ->where('status', 'batal')
                ->count(),

            // Jadwal mendatang
            'terjadwal' => Registration::whereDate(
                'tanggal_daftar',
                '>',
                $today
            )
                ->where('status', 'menunggu')
                ->count(),

            'total_pasien' => Patient::count(),

            'total_dokter' => Doctor::where('is_active', true)
                ->count(),

            'total_poli' => Department::where('is_active', true)
                ->count(),
        ];

        // ── Pendaftaran Per Poli ─────────────────────────────────────
        $pendaftaranPerPoli = (clone $baseQuery())
            ->with('department')
            ->select(
                'department_id',
                DB::raw('count(*) as total')
            )
            ->groupBy('department_id')
            ->get()
            ->map(function ($r) {
                return [
                    'nama_poli' => $r->department->nama_poli ?? '-',
                    'total' => $r->total,
                ];
            });

        // ── Grafik Pendaftaran 7 Hari Terakhir ───────────────────────
        $grafik = Registration::select(
            DB::raw('DATE(tanggal_daftar) as tanggal'),
            DB::raw('count(*) as total')
        )
            ->whereDate(
                'tanggal_daftar',
                '>=',
                now()->subDays(6)->format('Y-m-d')
            );

        if ($deptId) {
            $grafik->where('department_id', $deptId);
        }

        if ($doctorId) {
            $grafik->where('doctor_id', $doctorId);
        }

        if ($userId) {
            $grafik->where('created_by', $userId);
        }

        $pendaftaran7Hari = $grafik
            ->groupBy(DB::raw('DATE(tanggal_daftar)'))
            ->orderBy('tanggal')
            ->get()
            ->keyBy('tanggal');

        $labels7Hari = [];
        $data7Hari = [];

        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()
                ->subDays($i)
                ->format('Y-m-d');

            $labels7Hari[] = now()
                ->subDays($i)
                ->format('d M');

            $data7Hari[] = $pendaftaran7Hari
                ->get($tgl)?->total ?? 0;
        }

        // ── Antrian Terbaru Hari Ini ─────────────────────────────────
        $antrianTerbaru = Registration::with([
            'patient',
            'department',
            'doctor',
        ])
            ->whereDate('tanggal_daftar', $today)
            ->whereIn('status', [
                'menunggu',
                'dipanggil',
            ])
            ->orderBy('urutan_antrian')
            ->limit(10)
            ->get();

        // ── Ranking Poin Petugas ─────────────────────────────────────
        $rankingPetugas = null;

        if ($isAdmin) {
            $rankingPetugas = User::where('role', 'petugas')
                ->withSum(
                    'petugasPoints as total_poin_earned',
                    'points'
                )
                ->withSum(
                    [
                        'pointRedemptions as total_poin_redeemed' =>
                            function ($q) {
                                $q->where('status', 'selesai');
                            },
                    ],
                    'points'
                )
                ->withCount([
                    'registrations as total_pendaftaran_all',
                ])
                ->orderByDesc('total_poin_earned')
                ->limit(5)
                ->get()
                ->map(function ($u) {
                    $u->saldo_poin =
                        ($u->total_poin_earned ?? 0)
                        - ($u->total_poin_redeemed ?? 0);

                    return $u;
                });
        }
        
        $pendingRedemptions = collect();
        $pendingPointRequests = collect();
        $pendingPointRequestCount = 0;
        if ($isAdmin) {
            $pendingRedemptions = \App\Models\PointRedemption::with('user')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->get();
            $pendingPointRequests = \App\Models\PointRequest::with('user')
                ->where('status', 'pending')
                ->orderBy('created_at', 'asc')
                ->limit(5)
                ->get();
            $pendingPointRequestCount = \App\Models\PointRequest::where('status', 'pending')->count();
        }

        // ── Data Filter Dropdown ─────────────────────────────────────
        $filterDepts = $isAdmin
            ? Department::active()
                ->orderBy('nama_poli')
                ->get()
            : collect();

        $filterDoctors = $isAdmin
            ? Doctor::active()
                ->orderBy('nama_dokter')
                ->get()
            : collect();

        $filterPetugas = $isAdmin
            ? User::where('role', 'petugas')
                ->orderBy('name')
                ->get()
            : collect();

        // ── Filter Aktif ─────────────────────────────────────────────
        $activeFilters = compact(
            'dari',
            'sampai',
            'deptId',
            'doctorId',
            'userId'
        );

        // ══════════════════════════════════════════════════════════════
        // DATA POIN KARYAWAN
        // ══════════════════════════════════════════════════════════════

        $tahunPoin = now()->year;
        $bulanPoin = now()->month;

        // ── Total seluruh poin bulan berjalan ─────────────────────────
        $totalPoin = PetugasPoint::whereYear(
            'created_at',
            $tahunPoin
        )
            ->whereMonth(
                'created_at',
                $bulanPoin
            )
            ->sum('points');

        // ── Ranking poin petugas bulan berjalan ──────────────────────
        $rankingPoin = User::where('role', 'petugas')
            ->withSum(
                [
                    'petugasPoints as total_poin' =>
                        function ($query) use ($tahunPoin, $bulanPoin) {
                            $query
                                ->whereYear(
                                    'created_at',
                                    $tahunPoin
                                )
                                ->whereMonth(
                                    'created_at',
                                    $bulanPoin
                                );
                        },
                ],
                'points'
            )
            ->withCount(
                [
                    'petugasPoints as total_pendaftaran' =>
                        function ($query) use ($tahunPoin, $bulanPoin) {
                            $query
                                ->whereYear(
                                    'created_at',
                                    $tahunPoin
                                )
                                ->whereMonth(
                                    'created_at',
                                    $bulanPoin
                                );
                        },
                ]
            )
            ->orderByDesc('total_poin')
            ->get();

        // ── Petugas terbaik ──────────────────────────────────────────
        $petugasTerbaik = $rankingPoin->first();

        // ── Total pendaftaran yang menghasilkan poin ─────────────────
        $totalPendaftaranPoin = PetugasPoint::whereYear(
            'created_at',
            $tahunPoin
        )
            ->whereMonth(
                'created_at',
                $bulanPoin
            )
            ->count();

        // ── Nama bulan untuk Dashboard ───────────────────────────────
        $namaBulanPoin = now()->translatedFormat('F Y');

        // ── Kirim data ke dashboard ──────────────────────────────────
        return view('dashboard', compact(
            'stats',
            'pendaftaranPerPoli',
            'labels7Hari',
            'data7Hari',
            'antrianTerbaru',
            'rankingPetugas',
            'filterDepts',
            'filterDoctors',
            'filterPetugas',
            'activeFilters',
            'hasDateFilter',
            'dari',
            'sampai',

            // DATA POIN
            'totalPoin',
            'rankingPoin',
            'petugasTerbaik',
            'totalPendaftaranPoin',
            'namaBulanPoin',
            'pendingRedemptions',
            'pendingPointRequests',
            'pendingPointRequestCount'
        ));
    }
}