<?php

namespace App\Http\Controllers;

use App\Models\PetugasPoint;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PointController extends Controller
{
    /** Halaman "Poin Saya" — khusus petugas, lihat poin milik sendiri */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $totalPoin = Auth::user()->totalPoints();

        $riwayat = PetugasPoint::with(['department', 'registration.patient'])
            ->where('user_id', $userId)
            ->latest()
            ->get();

        $rekapPerPoli = PetugasPoint::with('department')
            ->where('user_id', $userId)
            ->selectRaw('department_id, SUM(points) as total')
            ->groupBy('department_id')
            ->get();

        return view('points.index', compact('totalPoin', 'riwayat', 'rekapPerPoli'));
    }

    /** Halaman "Poin Karyawan" — khusus admin, rekap semua petugas per bulan */
    public function admin(Request $request)
    {
        // Daftar bulan yang punya data poin
        $bulanTersedia = PetugasPoint::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as bulan")
            ->distinct()
            ->orderByDesc('bulan')
            ->pluck('bulan');

        // Fallback: kalau belum ada data poin sama sekali, tetap tampilkan bulan berjalan
        if ($bulanTersedia->isEmpty()) {
            $bulanTersedia = collect([now()->format('Y-m')]);
        }

        // Bulan yang dipilih (default: bulan terbaru yang ada datanya)
        $bulan = $request->input('bulan', $bulanTersedia->first());

        [$tahunFilter, $bulanFilter] = explode('-', $bulan);

        // Ranking total poin per petugas untuk bulan terpilih
        $rekapPetugas = User::where('role', 'petugas')
            ->withSum(['petugasPoints as total_poin' => function ($q) use ($tahunFilter, $bulanFilter) {
                $q->whereYear('created_at', $tahunFilter)
                  ->whereMonth('created_at', $bulanFilter);
            }], 'points')
            ->withCount(['petugasPoints as total_pendaftaran' => function ($q) use ($tahunFilter, $bulanFilter) {
                $q->whereYear('created_at', $tahunFilter)
                  ->whereMonth('created_at', $bulanFilter);
            }])
            ->orderByDesc('total_poin')
            ->get();

        // Riwayat gabungan semua petugas, ikut filter bulan + opsional filter petugas
        $query = PetugasPoint::with(['user', 'department', 'registration.patient'])
            ->whereYear('created_at', $tahunFilter)
            ->whereMonth('created_at', $bulanFilter)
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $riwayat = $query->get();

        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        return view('points.admin', compact(
            'rekapPetugas', 'riwayat', 'bulan', 'bulanTersedia', 'petugasList'
        ));
    }

    /** Proses penukaran poin oleh admin */
    public function redeem(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer|min:1',
            'type' => 'required|in:uang,merchandise'
        ]);

        $user = User::findOrFail($request->user_id);
        
        if ($user->totalPoints() < $request->points) {
            return back()->with('error', 'Saldo poin petugas tidak mencukupi untuk ditukar.');
        }

        \App\Models\PointRedemption::create([
            'user_id' => $user->id,
            'points' => $request->points,
            'type' => $request->type
        ]);

        return back()->with('success', 'Berhasil menukar ' . $request->points . ' poin milik ' . $user->name . ' menjadi ' . $request->type . '.');
    }
}