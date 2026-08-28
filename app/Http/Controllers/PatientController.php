<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /** Halaman Data Pasien Terdaftar */
    public function index(Request $request)
    {
        $query = Patient::withCount('registrations')->orderBy('nama_pasien');

        // Filter pencarian
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($q2) use ($q) {
                $q2->where('nama_pasien', 'like', "%{$q}%")
                   ->orWhere('nik', 'like', "%{$q}%")
                   ->orWhere('no_rm', 'like', "%{$q}%");
            });
        }

        $patients = $query->paginate(20)->withQueryString();

        return view('patients.index', compact('patients'));
    }

    /** Detail Pasien Terdaftar */
    public function show(Patient $patient)
    {
        $patient->load(['registrations' => function ($q) {
            $q->with(['department', 'doctor'])->orderByDesc('tanggal_daftar');
        }, 'creator']);

        return view('patients.show', compact('patient'));
    }
}
