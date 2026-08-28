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

        // Preview nomor antrian berikutnya
        $lastUrutan = Registration::where('department_id', $schedule->department_id)
            ->where('tanggal_daftar', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->max('urutan_antrian') ?? 0;

        $nomorBerikutnya = strtoupper($department->kode_poli) . str_pad($lastUrutan + 1, 3, '0', STR_PAD_LEFT);

        return response()->json([
            'valid'            => true,
            'sisa_kuota'       => $sisaKuota,
            'kuota_total'      => $schedule->kuota,
            'nomor_berikutnya' => $nomorBerikutnya,
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

    /**
     * Preview nomor antrian berikutnya untuk poli + tanggal tertentu.
     * GET /ajax/nomor-antrian?department_id=1&tanggal=2026-01-20
     */
    public function getNomorAntrian(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'tanggal'       => 'required|date',
        ]);

        $department = Department::findOrFail($request->department_id);
        $antrian    = $department->generateNomorAntrian($request->tanggal);

        return response()->json([
            'nomor_antrian' => $antrian['nomor_antrian'],
            'urutan'        => $antrian['urutan'],
        ]);
    }

    /**
     * Data antrian real-time per poli (untuk halaman display antrian).
     * GET /ajax/antrian/{department}
     */
    public function getAntrianPoli(Department $department)
    {
        $antrian = Registration::with('patient')
            ->where('department_id', $department->id)
            ->whereDate('tanggal_daftar', today())
            ->orderBy('urutan_antrian')
            ->get()
            ->map(fn($r) => [
                'id'            => $r->id,
                'nomor_antrian' => $r->nomor_antrian,
                'nama_pasien'   => $r->patient->nama_pasien,
                'status'        => $r->status,
                'status_label'  => $r->status_label,
                'status_badge'  => $r->status_badge,
                'updated_at'    => $r->updated_at->format('H:i'),
            ]);

        return response()->json([
            'department' => $department->nama_poli,
            'antrian'    => $antrian,
            'stats'      => [
                'menunggu'  => $antrian->where('status', 'menunggu')->count(),
                'dipanggil' => $antrian->where('status', 'dipanggil')->count(),
                'selesai'   => $antrian->where('status', 'selesai')->count(),
            ],
        ]);
    }
}
