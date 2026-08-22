<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PointTransaction;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PointAdjustmentController extends Controller
{
    public function __construct(
        protected PointService $pointService
    ) {}

    public function index(Request $request)
    {
        $petugasList = User::where('role', 'petugas')->orderBy('name')->get();

        $query = PointTransaction::with(['user', 'creator'])
            ->where('type', 'adjustment')
            ->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $adjustments = $query->paginate(15)->withQueryString();

        return view('admin.points.adjustment', compact('petugasList', 'adjustments'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'action'  => ['required', 'in:add,subtract'],
            'amount'  => ['required', 'integer', 'min:1', 'max:10000'],
            'reason'  => ['required', 'string', 'max:500'],
        ], [
            'user_id.required' => 'Pilih karyawan yang ingin disesuaikan poinnya.',
            'amount.required'  => 'Jumlah poin wajib diisi.',
            'amount.min'       => 'Jumlah poin minimal 1.',
            'reason.required'  => 'Alasan penyesuaian poin wajib diisi.',
        ]);

        $targetUser = User::findOrFail($validated['user_id']);
        $amount     = (int) $validated['amount'];
        if ($validated['action'] === 'subtract') {
            $amount = -$amount;
        }

        try {
            $transaction = $this->pointService->adjust(
                user: $targetUser,
                amount: $amount,
                reason: $validated['reason'],
                admin: Auth::user()
            );

            $actionText = $amount > 0 ? "+{$amount}" : "{$amount}";
            return redirect()->route('admin.points.adjustment.index')
                ->with('success', "Penyesuaian saldo poin ({$actionText}) untuk {$targetUser->name} berhasil dilakukan! Saldo baru: {$transaction->balance_after} poin.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }
}
