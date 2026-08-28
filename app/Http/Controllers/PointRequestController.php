<?php

namespace App\Http\Controllers;

use App\Models\PointRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PointRequestController extends Controller
{
    // ══════════════════════════════════════════════════════
    // PETUGAS
    // ══════════════════════════════════════════════════════

    /** Daftar pengajuan milik petugas yang login */
    public function index()
    {
        $user = Auth::user();

        $requests = PointRequest::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        $totalPoin = $user->totalPoints();

        return view('point-requests.index', compact('requests', 'totalPoin'));
    }

    /** Form buat pengajuan baru */
    public function create()
    {
        return view('point-requests.create');
    }

    /** Simpan pengajuan baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'points' => ['required', 'integer', 'min:1'],
            'reason' => ['required', 'string', 'max:1000'],
        ], [
            'points.required' => 'Jumlah poin wajib diisi.',
            'points.integer'  => 'Jumlah poin harus berupa angka bulat.',
            'points.min'      => 'Jumlah poin minimal adalah 1.',
            'reason.required' => 'Alasan pengajuan wajib diisi.',
            'reason.max'      => 'Alasan maksimal 1000 karakter.',
        ]);

        PointRequest::create([
            'user_id' => Auth::id(),
            'points'  => $validated['points'],
            'reason'  => $validated['reason'],
            'status'  => 'pending',
        ]);

        return redirect()->route('point-requests.index')
            ->with('success', 'Pengajuan poin berhasil dikirim. Menunggu persetujuan Admin.');
    }

    // ══════════════════════════════════════════════════════
    // ADMIN
    // ══════════════════════════════════════════════════════

    /** Daftar semua pengajuan (khusus Admin) */
    public function adminIndex(Request $request)
    {
        $query = PointRequest::with(['user', 'admin'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('created_at');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $requests = $query->paginate(15)->withQueryString();

        $pendingCount = PointRequest::where('status', 'pending')->count();

        return view('point-requests.admin', compact('requests', 'pendingCount'));
    }

    /** Admin menyetujui pengajuan */
    public function approve(Request $request, PointRequest $pointRequest)
    {
        // Jika sudah diproses, tolak
        if (!$pointRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        DB::transaction(function () use ($pointRequest) {
            $pointRequest->update([
                'status'      => 'approved',
                'admin_id'    => Auth::id(),
                'approved_at' => now(),
                'admin_note'  => null,
            ]);

            // Tambahkan poin ke petugas via PointService (sistem baru)
            // PointService::earn() akan update point_balance di tabel users secara atomik
            $pointService = app(\App\Services\PointService::class);
            $reference    = 'EARN-REQ-' . $pointRequest->id;
            $description  = "Poin disetujui dari pengajuan manual (ID #{$pointRequest->id}): {$pointRequest->reason}";

            $pointService->earn(
                user: \App\Models\User::findOrFail($pointRequest->user_id),
                amount: $pointRequest->points,
                sourceType: \App\Models\PointRequest::class,
                sourceId: $pointRequest->id,
                reference: $reference,
                description: $description,
                creator: \App\Models\User::find(Auth::id()),
            );
        });

        return back()->with('success',
            "Pengajuan {$pointRequest->points} poin dari {$pointRequest->user->name} berhasil disetujui dan poin telah ditambahkan."
        );
    }

    /** Admin menolak pengajuan */
    public function reject(Request $request, PointRequest $pointRequest)
    {
        if (!$pointRequest->isPending()) {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        $pointRequest->update([
            'status'      => 'rejected',
            'admin_id'    => Auth::id(),
            'rejected_at' => now(),
            'admin_note'  => $request->admin_note,
        ]);

        return back()->with('success',
            "Pengajuan poin dari {$pointRequest->user->name} telah ditolak."
        );
    }
}
