<?php

namespace App\Http\Controllers;

use App\Models\PetugasPoint;
use App\Models\PointRedemption;
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
            ->whereDate('created_at', '>=', today()->subMonth())
            ->latest()
            ->get();

        $rekapPerPoli = PetugasPoint::with('department')
            ->where('user_id', $userId)
            ->selectRaw('department_id, SUM(points) as total')
            ->groupBy('department_id')
            ->get();

        // Riwayat penukaran poin milik petugas ini
        $riwayatTukar = PointRedemption::where('user_id', $userId)
            ->latest()
            ->get();

        return view('points.index', compact('totalPoin', 'riwayat', 'rekapPerPoli', 'riwayatTukar'));
    }

    /** Halaman Katalog Hadiah (Petugas) */
    public function katalog()
    {
        $totalPoin = Auth::user()->totalPoints();
        $merchandises = \App\Models\Merchandise::orderBy('points', 'asc')->get();
        return view('points.katalog', compact('totalPoin', 'merchandises'));
    }

    /** Halaman Master Merchandise (Admin) */
    public function masterMerchandise()
    {
        $merchandises = \App\Models\Merchandise::orderBy('points', 'asc')->get();
        return view('points.merchandise', compact('merchandises'));
    }

    /** Proses tambah barang ke katalog */
    public function storeMerchandise(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'points'      => 'required|integer|min:1',
            'description' => 'required|string',
            'image'       => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('images/merchandise'), $imageName);
        }

        \App\Models\Merchandise::create([
            'name'        => $request->name,
            'points'      => $request->points,
            'description' => $request->description,
            'image'       => $imageName,
        ]);

        return back()->with('success', 'Barang berhasil ditambahkan ke dalam katalog!');
    }

    /** Hapus barang dari katalog */
    public function destroyMerchandise(\App\Models\Merchandise $merchandise)
    {
        // Hapus gambar jika ada
        if ($merchandise->image && file_exists(public_path('images/merchandise/' . $merchandise->image))) {
            unlink(public_path('images/merchandise/' . $merchandise->image));
        }
        
        $merchandise->delete();

        return back()->with('success', 'Barang berhasil dihapus dari katalog.');
    }

    /** Proses request penukaran poin dari sisi petugas */
    public function requestRedeem(Request $request)
    {
        $request->validate([
            'points'  => 'required|integer|min:1',
            'type'    => 'required|string|max:100',
            'qty'     => 'nullable|integer|min:1',
        ]);

        $user = Auth::user();

        $totalRedeemPoints = $request->points;
        $type = $request->type;

        if ($request->has('qty') && $request->qty > 1) {
            $totalRedeemPoints = $request->points * $request->qty;
            $type = $request->type . ' (x' . $request->qty . ')';
        }

        if ($user->totalPoints() < $totalRedeemPoints) {
            return back()->with('error', 'Saldo poin Anda tidak mencukupi untuk ditukar.');
        }

        $redemption = PointRedemption::create([
            'user_id' => $user->id,
            'points'  => $totalRedeemPoints,
            'type'    => $type,
            'status'  => 'pending',
            'catatan' => 'Request mandiri via Katalog',
        ]);

        return redirect()->route('points.redemption.cetak', $redemption)->with('success', 'Permintaan penukaran poin berhasil dikirim. Status saat ini: Menunggu konfirmasi Admin.');
    }

    /** Cetak resi penukaran poin */
    public function cetakResi(PointRedemption $redemption)
    {
        // Hanya pemilik atau admin yang boleh akses
        if (Auth::id() !== $redemption->user_id && !Auth::user()->isAdmin()) {
            abort(403, 'Anda tidak memiliki akses ke resi ini.');
        }

        $redemption->load('user');
        return view('points.cetak-resi', compact('redemption'));
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

        // Riwayat penukaran poin semua petugas (CRUD)
        $redemptionQuery = PointRedemption::with('user')->latest();
        if ($request->filled('filter_status')) {
            $redemptionQuery->where('status', $request->filter_status);
        }
        if ($request->filled('filter_user_id')) {
            $redemptionQuery->where('user_id', $request->filter_user_id);
        }
        $riwayatTukar = $redemptionQuery->paginate(15, ['*'], 'tukar_page')->withQueryString();

        return view('points.admin', compact(
            'rekapPetugas', 'riwayat', 'bulan', 'bulanTersedia', 'petugasList', 'riwayatTukar'
        ));
    }

    /** Proses penukaran poin baru oleh admin */
    public function redeem(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'points'  => 'required|integer|min:1',
            'type'    => 'required|string|max:100',
            'catatan' => 'nullable|string|max:500',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->totalPoints() < $request->points) {
            return back()->with('error', 'Saldo poin petugas tidak mencukupi untuk ditukar.');
        }

        PointRedemption::create([
            'user_id' => $user->id,
            'points'  => $request->points,
            'type'    => $request->type,
            'status'  => 'pending',   // selalu mulai dari pending
            'catatan' => $request->catatan,
        ]);

        return back()->with('success', 'Permintaan penukaran ' . $request->points . ' poin milik ' . $user->name . ' berhasil dibuat. Status: Menunggu konfirmasi.');
    }

    /** Update status penukaran poin (admin) */
    public function updateRedemption(Request $request, PointRedemption $redemption)
    {
        $request->validate([
            'status'  => 'required|in:pending,disetujui,selesai,ditolak',
            'catatan' => 'nullable|string|max:500',
        ]);

        // Transaksi yang sudah final tidak bisa diubah
        if ($redemption->isFinal()) {
            return back()->with('error', 'Penukaran yang sudah ' . $redemption->status_label . ' tidak dapat diubah lagi.');
        }

        // Validasi saldo cukup jika status diubah ke selesai
        if ($request->status === 'selesai') {
            $user = $redemption->user;
            // Saldo dihitung tanpa memperhitungkan redemption ini (belum selesai)
            $saldoTanpaIni = $user->petugasPoints()->sum('points')
                           - $user->pointRedemptions()
                                  ->where('status', 'selesai')
                                  ->where('id', '!=', $redemption->id)
                                  ->sum('points');

            if ($saldoTanpaIni < $redemption->points) {
                return back()->with('error', 'Saldo poin petugas tidak mencukupi untuk menyelesaikan penukaran ini.');
            }
        }

        $redemption->update([
            'status'  => $request->status,
            'catatan' => $request->catatan ?? $redemption->catatan,
        ]);

        return back()->with('success', 'Status penukaran berhasil diperbarui menjadi: ' . $redemption->fresh()->status_label);
    }

    /** Hapus penukaran poin (hanya yang masih pending) */
    public function destroyRedemption(PointRedemption $redemption)
    {
        if ($redemption->status !== 'pending') {
            return back()->with('error', 'Hanya penukaran berstatus Menunggu yang dapat dihapus. Status saat ini: ' . $redemption->status_label);
        }

        $redemption->delete();

        return back()->with('success', 'Data penukaran poin berhasil dihapus.');
    }
}