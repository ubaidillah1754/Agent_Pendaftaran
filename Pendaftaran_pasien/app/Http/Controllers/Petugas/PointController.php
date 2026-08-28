<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\PointRedemption;
use App\Models\PointTransaction;
use App\Services\RedemptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointController extends Controller
{
    public function __construct(
        protected RedemptionService $redemptionService
    ) {}

    /**
     * Dashboard Poin Saya
     */
    public function index()
    {
        $user = Auth::user();

        $saldoPoin     = $user->totalPoints();
        $totalPasien   = $user->createdPatients()->count();
        $totalEarned   = $user->totalEarnedPoints();
        $totalRedeemed = $user->totalRedeemedPoints();

        // Mutasi poin terbaru (5 terakhir)
        $recentTransactions = PointTransaction::where('user_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Merchandise unggulan / reward tersedia
        $featuredRewards = Merchandise::active()
            ->inStock()
            ->orderBy('points_required')
            ->limit(4)
            ->get();

        return view('petugas.points.index', compact(
            'user',
            'saldoPoin',
            'totalPasien',
            'totalEarned',
            'totalRedeemed',
            'recentTransactions',
            'featuredRewards'
        ));
    }

    /**
     * Riwayat Mutasi Poin Lengkap
     */
    public function riwayat(Request $request)
    {
        $user = Auth::user();

        $query = PointTransaction::where('user_id', $user->id)
            ->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $transactions = $query->paginate(15)->withQueryString();

        return view('petugas.points.riwayat', compact('user', 'transactions'));
    }

    /**
     * Katalog Reward / Merchandise
     */
    public function katalog(Request $request)
    {
        $user = Auth::user();

        $query = Merchandise::active()->orderBy('points_required');

        if ($request->filled('q')) {
            $query->where('name', 'like', "%{$request->q}%")
                  ->orWhere('description', 'like', "%{$request->q}%");
        }

        $rewards = $query->paginate(12)->withQueryString();

        return view('petugas.points.katalog', compact('user', 'rewards'));
    }

    /**
     * Submit Penukaran Reward
     */
    public function tukar(Request $request)
    {
        $validated = $request->validate([
            'merchandise_id' => ['required', 'exists:merchandises,id'],
            'quantity'       => ['required', 'integer', 'min:1', 'max:50'],
            'notes'          => ['nullable', 'string', 'max:500'],
        ], [
            'merchandise_id.required' => 'Pilih reward yang ingin ditukarkan.',
            'quantity.min'            => 'Jumlah penukaran minimal 1.',
        ]);

        try {
            $redemption = $this->redemptionService->createRedemption(
                user: Auth::user(),
                merchandiseId: (int) $validated['merchandise_id'],
                quantity: (int) $validated['quantity'],
                notes: $validated['notes'] ?? null
            );

            return redirect()->route('points.redemptions.index')
                ->with('success', "Permohonan penukaran {$redemption->merchandise_name} ({$redemption->quantity}x) berhasil diajukan! Kode: {$redemption->reference_code}.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Riwayat Penukaran Reward Milik Petugas
     */
    public function riwayatRedemption(Request $request)
    {
        $user = Auth::user();

        $query = PointRedemption::with('merchandise')
            ->where('user_id', $user->id)
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $redemptions = $query->paginate(15)->withQueryString();

        return view('petugas.points.redemptions', compact('user', 'redemptions'));
    }

    /**
     * Cetak Resi Penukaran Reward
     */
    public function cetakResi(PointRedemption $redemption)
    {
        // Pastikan hanya pemilik atau admin yang bisa mencetak
        if (!Auth::user()->isAdmin() && Auth::id() !== $redemption->user_id) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $redemption->load(['user', 'merchandise', 'approver']);

        return view('petugas.points.cetak-resi', compact('redemption'));
    }
}
