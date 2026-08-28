<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use Illuminate\Http\Request;

class AjaxController extends Controller
{
    /**
     * Ambil daftar dokter aktif berdasarkan poli yang dipilih.
     * Dipanggil saat user memilih poli di form pendaftaran.
     * GET /ajax/doctors?department_id=1
     */
    public function getDoctors(Request $request)
    {
        $request->validate(['department_id' => 'required|exists:departments,id']);

        $doctors = Doctor::active()
            ->where('department_id', $request->department_id)
            ->select('id', 'nama_dokter', 'spesialisasi')
            ->orderBy('nama_dokter')
            ->get();

        return response()->json($doctors);
    }

    /**
     * Ambil jadwal aktif dokter yang dipilih.
     * Dipanggil saat user memilih dokter di form pendaftaran.
     * GET /ajax/schedules?doctor_id=1&department_id=1
     */
    public function getSchedules(Request $request)
    {
        $request->validate([
            'doctor_id'     => 'required|exists:doctors,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $schedules = DoctorSchedule::active()
            ->where('doctor_id', $request->doctor_id)
            ->where('department_id', $request->department_id)
            ->select('id', 'hari', 'jam_mulai', 'jam_selesai', 'kuota')
            ->orderByRaw("FIELD(hari, 'Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu')")
            ->get()
            ->map(fn($s) => [
                'id'          => $s->id,
                'hari'        => $s->hari,
                'jam_mulai'   => substr($s->jam_mulai, 0, 5),
                'jam_selesai' => substr($s->jam_selesai, 0, 5),
                'kuota'       => $s->kuota,
                'label'       => "{$s->hari}, " . substr($s->jam_mulai, 0, 5) . " – " . substr($s->jam_selesai, 0, 5) . " (Kuota: {$s->kuota})",
            ]);

        return response()->json($schedules);
    }

    /**
     * Cek sisa kuota jadwal pada tanggal tertentu + preview nomor antrian.
     * Dipanggil saat user memilih jadwal + tanggal.
     * GET /ajax/kuota?schedule_id=1&tanggal=2026-01-20
     */
    public function getKuota(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:doctor_schedules,id',
            'tanggal'     => 'required|date|after_or_equal:today',
        ]);

        $schedule   = DoctorSchedule::with('department')->findOrFail($request->schedule_id);
        $tanggal    = $request->tanggal;
        $hariDaftar = DoctorSchedule::hariDariTanggal($tanggal);

        // Cek kesesuaian hari jadwal vs tanggal pilihan
        if ($schedule->hari !== $hariDaftar) {
            return response()->json([
                'valid'   => false,
                'message' => "Jadwal ini hanya tersedia hari {$schedule->hari}. Tanggal yang Anda pilih adalah hari {$hariDaftar}.",
            ]);
        }

        $sisaKuota = $schedule->sisaKuota($tanggal);
        $department = $schedule->department;

        return response()->json([
            'valid'            => true,
            'sisa_kuota'       => $sisaKuota,
            'kuota_total'      => $schedule->kuota,
            'penuh'            => $sisaKuota <= 0,
            'message'          => $sisaKuota > 0
                ? "Sisa kuota: {$sisaKuota} dari {$schedule->kuota}"
                : 'Kuota pendaftaran sudah penuh.',
        ]);
    }

    /**
     * Live search pasien by NIK, No. RM, atau nama.
     * Dipanggil dari input pencarian di form pendaftaran pasien lama.
     * GET /ajax/cari-pasien?q=3271...
     */
    public function cariPasien(Request $request)
    {
        $request->validate(['q' => 'required|string|min:3']);

        $q = $request->q;
        $patients = Patient::where('nik', 'like', "%{$q}%")
            ->orWhere('no_rm', 'like', "%{$q}%")
            ->orWhere('nama_pasien', 'like', "%{$q}%")
            ->select('id', 'no_rm', 'nik', 'nama_pasien', 'tanggal_lahir', 'jenis_kelamin', 'jenis_pembayaran', 'alamat')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'              => $p->id,
                'no_rm'           => $p->no_rm,
                'nik'             => $p->nik,
                'nama_pasien'     => $p->nama_pasien,
                'tanggal_lahir'   => $p->tanggal_lahir->format('d M Y'),
                'jenis_kelamin'   => $p->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
                'jenis_pembayaran'=> strtoupper($p->jenis_pembayaran),
                'alamat'          => $p->alamat,
            ]);

        return response()->json($patients);
    }
}
