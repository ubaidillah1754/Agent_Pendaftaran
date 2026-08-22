<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Merchandise;
use App\Models\PointRedemption;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PointReportController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->get('tab', 'karyawan');

        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');
        $userId = $request->get('user_id');

        // List karyawan untuk dropdown filter
        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        // ── 1. Laporan Karyawan & Saldo ─────────────────────────────────────
        $karyawanReport = User::where('role', 'petugas')
            ->withCount(['createdPatients as total_pasien_diinput' => function ($q) use ($dari, $sampai) {
                if ($dari) $q->whereDate('created_at', '>=', $dari);
                if ($sampai) $q->whereDate('created_at', '<=', $sampai);
            }])
            ->withSum(['pointTransactions as total_earned' => function ($q) use ($dari, $sampai) {
                $q->where('type', 'earn');
                if ($dari) $q->whereDate('created_at', '>=', $dari);
                if ($sampai) $q->whereDate('created_at', '<=', $sampai);
            }], 'amount')
            ->withSum(['pointTransactions as total_redeemed' => function ($q) use ($dari, $sampai) {
                $q->where('type', 'redeem');
                if ($dari) $q->whereDate('created_at', '>=', $dari);
                if ($sampai) $q->whereDate('created_at', '<=', $sampai);
            }], 'amount')
            ->orderByDesc('point_balance')
            ->get();

        // ── 2. Laporan Ledger Transaksi Poin ────────────────────────────────
        $ledgerQuery = PointTransaction::with(['user', 'creator'])->latest();
        if ($userId) {
            $ledgerQuery->where('user_id', $userId);
        }
        if ($request->filled('type')) {
            $ledgerQuery->where('type', $request->type);
        }
        if ($dari) {
            $ledgerQuery->whereDate('created_at', '>=', $dari);
        }
        if ($sampai) {
            $ledgerQuery->whereDate('created_at', '<=', $sampai);
        }
        $ledgerTransactions = $ledgerQuery->paginate(20, ['*'], 'ledger_page')->withQueryString();

        // Ringkasan mutasi
        $ledgerStats = [
            'total_earn'       => PointTransaction::where('type', 'earn')->sum('amount'),
            'total_redeem'     => abs(PointTransaction::where('type', 'redeem')->sum('amount')),
            'total_adjustment' => PointTransaction::where('type', 'adjustment')->sum('amount'),
            'total_reversal'   => PointTransaction::where('type', 'reversal')->sum('amount'),
        ];

        // ── 3. Laporan Penukaran Merchandise ────────────────────────────────
        $redemptionQuery = PointRedemption::with(['user', 'merchandise', 'approver'])->latest();
        if ($userId) {
            $redemptionQuery->where('user_id', $userId);
        }
        if ($request->filled('status')) {
            $redemptionQuery->where('status', $request->status);
        }
        if ($dari) {
            $redemptionQuery->whereDate('created_at', '>=', $dari);
        }
        if ($sampai) {
            $redemptionQuery->whereDate('created_at', '<=', $sampai);
        }
        $redemptionList = $redemptionQuery->paginate(20, ['*'], 'redemption_page')->withQueryString();

        // ── 4. Laporan Stok Merchandise ────────────────────────────────────
        $merchandiseReport = Merchandise::withTrashed()
            ->withCount(['redemptions as total_redeemed_count' => function ($q) {
                $q->whereIn('status', ['approved', 'completed']);
            }])
            ->withSum(['redemptions as total_points_used' => function ($q) {
                $q->whereIn('status', ['approved', 'completed']);
            }], 'total_points')
            ->orderBy('points_required')
            ->get();

        return view('admin.points.reports', compact(
            'tab',
            'petugasList',
            'karyawanReport',
            'ledgerTransactions',
            'ledgerStats',
            'redemptionList',
            'merchandiseReport',
            'dari',
            'sampai',
            'userId'
        ));
    }
}
