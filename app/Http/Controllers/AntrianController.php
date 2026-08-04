<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Registration;
use Illuminate\Http\Request;

class AntrianController extends Controller
{
    /** Halaman monitor antrian per poli */
    public function index(Request $request)
    {
        $departments    = Department::active()->orderBy('nama_poli')->get();
        $selectedDeptId = $request->input('department_id', $departments->first()?->id);
        $selectedDept   = $departments->find($selectedDeptId);

        $antrian = [];
        if ($selectedDept) {
            $antrian = Registration::with(['patient', 'doctor'])
                ->where('department_id', $selectedDeptId)
                ->whereDate('tanggal_daftar', today())
                ->orderBy('urutan_antrian')
                ->get();
        }

        // Statistik ringkas
        $stats = [
            'menunggu'  => collect($antrian)->where('status', 'menunggu')->count(),
            'dipanggil' => collect($antrian)->where('status', 'dipanggil')->count(),
            'selesai'   => collect($antrian)->where('status', 'selesai')->count(),
            'batal'     => collect($antrian)->where('status', 'batal')->count(),
        ];

        return view('antrian.index', compact('departments', 'selectedDept', 'antrian', 'stats'));
    }

    /** Panggil pasien berikutnya (menunggu → dipanggil) */
    public function panggil(Registration $registration)
    {
        if ($registration->status !== 'menunggu') {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak dalam status menunggu.',
            ], 422);
        }

        $registration->update(['status' => 'dipanggil']);

        return response()->json([
            'success'        => true,
            'message'        => "Pasien {$registration->nomor_antrian} dipanggil.",
            'nomor_antrian'  => $registration->nomor_antrian,
            'nama_pasien'    => $registration->patient->nama_pasien,
            'status'         => 'dipanggil',
        ]);
    }

    /** Tandai selesai (dipanggil → selesai) */
    public function selesai(Registration $registration)
    {
        if ($registration->status !== 'dipanggil') {
            return response()->json([
                'success' => false,
                'message' => 'Pasien belum dipanggil.',
            ], 422);
        }

        $registration->update(['status' => 'selesai']);

        return response()->json([
            'success'       => true,
            'message'       => "Antrian {$registration->nomor_antrian} selesai.",
            'nomor_antrian' => $registration->nomor_antrian,
            'status'        => 'selesai',
        ]);
    }

    /** Update status generik via AJAX (digunakan dari form tabel) */
    public function updateStatus(Request $request, Registration $registration)
    {
        $request->validate([
            'status' => ['required', 'in:menunggu,dipanggil,selesai,batal'],
        ]);

        $newStatus    = $request->status;
        $transisiSah  = Registration::transisiStatusValid();
        $allowed      = $transisiSah[$registration->status] ?? [];

        if (!in_array($newStatus, $allowed)) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat mengubah status dari '{$registration->status}' ke '{$newStatus}'.",
            ], 422);
        }

        $registration->update(['status' => $newStatus]);

        return response()->json([
            'success'       => true,
            'message'       => 'Status antrian diperbarui.',
            'status'        => $registration->status,
            'status_label'  => $registration->status_label,
            'status_badge'  => $registration->status_badge,
        ]);
    }

    /**
     * Halaman display antrian publik per poli.
     * Halaman ini bisa dipasang di TV/monitor di ruang tunggu.
     */
    public function display(Department $department)
    {
        $sedangDipanggil = Registration::with('patient')
            ->where('department_id', $department->id)
            ->whereDate('tanggal_daftar', today())
            ->where('status', 'dipanggil')
            ->orderByDesc('updated_at')
            ->first();

        $menunggu = Registration::with('patient')
            ->where('department_id', $department->id)
            ->whereDate('tanggal_daftar', today())
            ->where('status', 'menunggu')
            ->orderBy('urutan_antrian')
            ->limit(5)
            ->get();

        return view('antrian.display', compact('department', 'sedangDipanggil', 'menunggu'));
    }
}
