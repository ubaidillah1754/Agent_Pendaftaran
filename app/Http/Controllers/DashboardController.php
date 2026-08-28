<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\PointRedemption;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /** Halaman utama dashboard dengan ringkasan statistik */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $user->isAdmin();

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
            'total_pasien' => Patient::count(),
            'total_dokter' => Doctor::where('is_active', true)->count(),
            'total_poli'   => Department::where('is_active', true)->count(),
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

        // ── Data Filter Dropdown ─────────────────────────────────────
        $filterDepts = $isAdmin
            ? Department::active()->orderBy('nama_poli')->get()
            : collect();

        $filterDoctors = $isAdmin
            ? Doctor::active()->orderBy('nama_dokter')->get()
            : collect();

        $filterPetugas = $isAdmin
            ? User::where('role', 'petugas')->orderBy('name')->get()
            : collect();

        // ── Data Tambahan Poin & Reward ──────────────────────────────
        $pendingRedemptionsCount = 0;
        $topPetugas = collect();
        $myPointStats = [];

        if ($isAdmin) {
            $pendingRedemptionsCount = PointRedemption::where('status', 'pending')->count();
            $topPetugas = User::where('role', 'petugas')
                ->withCount('createdPatients')
                ->orderByDesc('point_balance')
                ->limit(5)
                ->get();
        } else {
            $myPointStats = [
                'point_balance'   => $user->point_balance,
                'patients_count'  => $user->createdPatients()->count(),
                'redeemed_count'  => $user->pointRedemptions()->count(),
            ];
        }

        // ── Filter Aktif ─────────────────────────────────────────────
        $activeFilters = compact(
            'dari',
            'sampai',
            'deptId',
            'doctorId',
            'userId'
        );

        // ── Kirim data ke dashboard ──────────────────────────────────
        return view('dashboard', compact(
            'stats',
            'pendaftaranPerPoli',
            'labels7Hari',
            'data7Hari',
            'filterDepts',
            'filterDoctors',
            'filterPetugas',
            'activeFilters',
            'hasDateFilter',
            'dari',
            'sampai',
            'pendingRedemptionsCount',
            'topPetugas',
            'myPointStats'
        ));
    }
}