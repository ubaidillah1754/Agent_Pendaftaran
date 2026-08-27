<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AntrianController
 *
 * Mengelola tampilan dan aksi antrean untuk staff/admin:
 * - Tampilan daftar antrean per poli per tanggal
 * - Memanggil pasien (menunggu → diperiksa)
 * - Menyelesaikan pemeriksaan (diperiksa → selesai)
 */
class AntrianController extends Controller
{
    /**
     * Halaman utama antrean — tampilkan daftar antrean hari ini.
     */
    public function index(Request $request)
    {
        $tanggal      = $request->input('tanggal', today()->toDateString());
        $departmentId = $request->input('department_id');

        $departments = Department::active()->orderBy('nama_poli')->get();

        // Base query: hanya yang sudah punya nomor antrean, untuk tanggal kunjungan tsb
        $query = Registration::with(['patient', 'department', 'doctor', 'doctorSchedule'])
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->whereNotNull('nomor_antrian')
            ->whereNotIn('status', ['batal']);

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        // Urutkan: A01, A02, A03, ... (sort by urutan numerik di nomor_antrian)
        $query->orderByRaw("CAST(SUBSTRING(nomor_antrian, 2) AS UNSIGNED) ASC");

        $antrean = $query->get();

        // Pisahkan berdasarkan status
        $menunggu   = $antrean->whereIn('status', ['menunggu']);
        $diperiksa  = $antrean->where('status', 'diperiksa');
        $selesai    = $antrean->where('status', 'selesai');

        // Stats hari ini
        $stats = [
            'total'     => $antrean->count(),
            'menunggu'  => $menunggu->count(),
            'diperiksa' => $diperiksa->count(),
            'selesai'   => $selesai->count(),
        ];

        return view('antrian.index', compact(
            'antrean', 'menunggu', 'diperiksa', 'selesai',
            'departments', 'departmentId', 'tanggal', 'stats'
        ));
    }

    /**
     * Panggil pasien: menunggu → diperiksa
     */
    public function panggil(Registration $registration)
    {
        if ($registration->status !== 'menunggu') {
            return back()->with('error', "Pasien {$registration->nomor_antrian} tidak dalam status menunggu.");
        }

        if (!$registration->nomor_antrian) {
            return back()->with('error', 'Pasien ini belum memiliki nomor antrean.');
        }

        $registration->update(['status' => 'diperiksa']);

        return back()->with('success',
            "Pasien {$registration->nomor_antrian} — {$registration->patient->nama_pasien} dipanggil untuk diperiksa."
        );
    }

    /**
     * Selesaikan pemeriksaan: diperiksa → selesai
     */
    public function selesai(Registration $registration)
    {
        if ($registration->status !== 'diperiksa') {
            return back()->with('error', "Pasien {$registration->nomor_antrian} tidak dalam status diperiksa.");
        }

        $registration->update(['status' => 'selesai']);

        return back()->with('success',
            "Pasien {$registration->nomor_antrian} — {$registration->patient->nama_pasien} selesai diperiksa."
        );
    }

    /**
     * Kembalikan pasien diperiksa ke menunggu (undo panggil)
     */
    public function tunda(Registration $registration)
    {
        if ($registration->status !== 'diperiksa') {
            return back()->with('error', "Pasien tidak dalam status diperiksa.");
        }

        $registration->update(['status' => 'menunggu']);

        return back()->with('success',
            "Pasien {$registration->nomor_antrian} dikembalikan ke status menunggu."
        );
    }

    /**
     * Update status antrean secara fleksibel (untuk admin)
     */
    public function updateStatus(Request $request, Registration $registration)
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,diperiksa,selesai,batal'],
        ]);

        $newStatus     = $request->status;
        $currentStatus = $registration->status;
        $transisi      = Registration::transisiStatusValid();

        if (!in_array($newStatus, $transisi[$currentStatus] ?? [])) {
            // Admin bisa override jika diperlukan — cek role
            if (!Auth::user()->isAdmin()) {
                return back()->with('error',
                    "Transisi status dari {$currentStatus} ke {$newStatus} tidak valid."
                );
            }
        }

        $registration->update(['status' => $newStatus]);

        return back()->with('success', "Status antrean diperbarui menjadi {$registration->fresh()->status_label}.");
    }
}
