<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\PointRedemption;
use App\Models\User;
use App\Services\RedemptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointRedemptionController extends Controller
{
    public function __construct(
        protected RedemptionService $redemptionService
    ) {}

    public function index(Request $request)
    {
        $query = PointRedemption::with(['user', 'merchandise', 'approver'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('merchandise_id')) {
            $query->where('merchandise_id', $request->merchandise_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $redemptions  = $query->paginate(20)->withQueryString();
        $petugasList  = User::where('role', 'petugas')->orderBy('name')->get();
        $merchandises = Merchandise::withTrashed()->orderBy('name')->get();

        $stats = [
            'pending'   => PointRedemption::where('status', 'pending')->count(),
            'approved'  => PointRedemption::where('status', 'approved')->count(),
            'completed' => PointRedemption::where('status', 'completed')->count(),
            'rejected'  => PointRedemption::where('status', 'rejected')->count(),
            'cancelled' => PointRedemption::where('status', 'cancelled')->count(),
        ];

        return view('admin.redemptions.index', compact('redemptions', 'petugasList', 'merchandises', 'stats'));
    }

    public function show(PointRedemption $redemption)
    {
        $redemption->load(['user', 'merchandise', 'approver', 'pointTransactions']);
        return view('admin.redemptions.show', compact('redemption'));
    }

    public function approve(Request $request, PointRedemption $redemption)
    {
        try {
            $this->redemptionService->approve(
                redemption: $redemption,
                admin: Auth::user(),
                notes: $request->input('notes')
            );

            return back()->with('success', "Penukaran {$redemption->reference_code} berhasil disetujui.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function complete(Request $request, PointRedemption $redemption)
    {
        try {
            $this->redemptionService->complete(
                redemption: $redemption,
                admin: Auth::user(),
                notes: $request->input('notes')
            );

            return back()->with('success', "Penukaran {$redemption->reference_code} ditandai selesai.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function reject(Request $request, PointRedemption $redemption)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Alasan penolakan wajib diisi.',
        ]);

        try {
            $this->redemptionService->reject(
                redemption: $redemption,
                admin: Auth::user(),
                reason: $request->reason
            );

            return back()->with('success', "Penukaran {$redemption->reference_code} berhasil ditolak dan poin telah dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function cancel(Request $request, PointRedemption $redemption)
    {
        $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], [
            'reason.required' => 'Alasan pembatalan wajib diisi.',
        ]);

        try {
            $this->redemptionService->cancel(
                redemption: $redemption,
                actor: Auth::user(),
                reason: $request->reason
            );

            return back()->with('success', "Penukaran {$redemption->reference_code} berhasil dibatalkan dan poin telah dikembalikan.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
