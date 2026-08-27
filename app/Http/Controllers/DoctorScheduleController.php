<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function index()
    {
        $schedules = DoctorSchedule::with(['doctor', 'department'])
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->get()
            ->groupBy('hari');

        return view('doctor-schedules.index', compact('schedules'));
    }

    public function create()
    {
        $departments = Department::active()->orderBy('nama_poli')->get();
        $doctors     = Doctor::active()->with('department')->orderBy('nama_dokter')->get();
        $hariList    = DoctorSchedule::daftarHari();

        return view('doctor-schedules.create', compact('departments', 'doctors', 'hariList'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'doctor_id'     => ['required', 'exists:doctors,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'hari'          => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai'     => ['required', 'date_format:H:i'],
            'jam_selesai'   => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota'         => ['required', 'integer', 'min:1', 'max:100'],
            'is_active'     => ['boolean'],
        ], [
            'doctor_id.required'     => 'Dokter wajib dipilih.',
            'department_id.required' => 'Poli wajib dipilih.',
            'hari.required'          => 'Hari praktik wajib dipilih.',
            'jam_mulai.required'     => 'Jam mulai wajib diisi.',
            'jam_selesai.after'      => 'Jam selesai harus setelah jam mulai.',
            'kuota.required'         => 'Kuota wajib diisi.',
            'kuota.min'              => 'Kuota minimal 1 pasien.',
            'kuota.max'              => 'Kuota maksimal 100 pasien.',
        ]);

        // Cek duplikasi jadwal dokter di hari & poli yang sama
        $duplikat = DoctorSchedule::where('doctor_id', $validated['doctor_id'])
            ->where('department_id', $validated['department_id'])
            ->where('hari', $validated['hari'])
            ->exists();

        if ($duplikat) {
            return back()->withInput()
                ->withErrors(['hari' => 'Dokter ini sudah memiliki jadwal di poli dan hari yang sama.']);
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        DoctorSchedule::create($validated);

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'Jadwal praktik berhasil ditambahkan.');
    }

    public function show(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->load(['doctor', 'department', 'registrations.patient']);
        return view('doctor-schedules.show', compact('doctorSchedule'));
    }

    public function edit(DoctorSchedule $doctorSchedule)
    {
        $departments = Department::active()->orderBy('nama_poli')->get();
        $doctors     = Doctor::active()->orderBy('nama_dokter')->get();
        $hariList    = DoctorSchedule::daftarHari();

        return view('doctor-schedules.edit', compact('doctorSchedule', 'departments', 'doctors', 'hariList'));
    }

    public function update(Request $request, DoctorSchedule $doctorSchedule)
    {
        $validated = $request->validate([
            'doctor_id'     => ['required', 'exists:doctors,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'hari'          => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai'     => ['required', 'date_format:H:i'],
            'jam_selesai'   => ['required', 'date_format:H:i', 'after:jam_mulai'],
            'kuota'         => ['required', 'integer', 'min:1', 'max:100'],
            'is_active'     => ['boolean'],
        ]);

        // Cek duplikasi (kecuali jadwal yang sedang diedit)
        $duplikat = DoctorSchedule::where('doctor_id', $validated['doctor_id'])
            ->where('department_id', $validated['department_id'])
            ->where('hari', $validated['hari'])
            ->where('id', '!=', $doctorSchedule->id)
            ->exists();

        if ($duplikat) {
            return back()->withInput()
                ->withErrors(['hari' => 'Dokter ini sudah memiliki jadwal di poli dan hari yang sama.']);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $doctorSchedule->update($validated);

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'Jadwal praktik berhasil diperbarui.');
    }

    public function destroy(DoctorSchedule $doctorSchedule)
    {
        // Cegah hapus jika masih ada pendaftaran aktif pada jadwal ini
        $ada = $doctorSchedule->registrations()
            ->whereDate('tanggal_daftar', today())
            ->whereIn('status', ['menunggu', 'diperiksa'])
            ->exists();

        if ($ada) {
            return back()->with('error', 'Jadwal tidak dapat dihapus karena masih ada pendaftaran aktif hari ini.');
        }

        $doctorSchedule->delete();

        return redirect()->route('doctor-schedules.index')
            ->with('success', 'Jadwal praktik berhasil dihapus.');
    }

    public function toggleActive(DoctorSchedule $doctorSchedule)
    {
        $doctorSchedule->update(['is_active' => !$doctorSchedule->is_active]);

        return response()->json([
            'success'   => true,
            'is_active' => $doctorSchedule->is_active,
            'message'   => 'Status jadwal diperbarui.',
        ]);
    }
}
