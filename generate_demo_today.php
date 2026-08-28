<?php
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

use App\Models\Patient;
use App\Models\DoctorSchedule;
use App\Models\Registration;

$today = now()->toDateString();

$hariMap = [
    'Sunday'    => 'Minggu',
    'Monday'    => 'Senin',
    'Tuesday'   => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday'  => 'Kamis',
    'Friday'    => 'Jumat',
    'Saturday'  => 'Sabtu',
];
$hariIni = $hariMap[now()->format('l')];

echo "=== Generate Demo Data Pendaftaran Hari Ini ===\n";
echo "Tanggal : $today | Hari : $hariIni\n\n";

$jadwalHariIni = DoctorSchedule::with(['doctor', 'department'])->where('hari', $hariIni)->get();

if ($jadwalHariIni->isEmpty()) {
    echo "[ERROR] Tidak ada jadwal dokter untuk hari $hariIni.\nSemua jadwal:\n";
    DoctorSchedule::with(['doctor', 'department'])->get()->each(function($s) {
        echo "  - {$s->hari}: {$s->doctor->nama_dokter} ({$s->department->nama_poli})\n";
    });
    exit(1);
}

echo "Jadwal tersedia hari ini:\n";
foreach ($jadwalHariIni as $j) {
    echo "  [{$j->id}] {$j->doctor->nama_dokter} ({$j->department->nama_poli}) {$j->jam_mulai}-{$j->jam_selesai}\n";
}

$patients = Patient::orderBy('id')->get();
echo "\nTotal pasien tersedia: {$patients->count()}\n\n";

if ($patients->count() < 1) {
    echo "[ERROR] Tidak ada data pasien.\n";
    exit(1);
}

$petugas = \App\Models\User::first();
$created = 0;

for ($i = 0; $i < 10; $i++) {
    $patient = $patients[$i % $patients->count()];
    $jadwal  = $jadwalHariIni[$i % $jadwalHariIni->count()];

    $existing = Registration::where('patient_id', $patient->id)
        ->where('doctor_schedule_id', $jadwal->id)
        ->whereDate('tanggal_kunjungan', $today)
        ->exists();

    if ($existing) {
        // Coba shift ke pasien berikutnya
        $patient = $patients[($i + $patients->count() / 2) % $patients->count()];
        $existing2 = Registration::where('patient_id', $patient->id)
            ->where('doctor_schedule_id', $jadwal->id)
            ->whereDate('tanggal_kunjungan', $today)
            ->exists();
        if ($existing2) {
            echo "  [$i] SKIP: duplikasi, tidak ada pasien lain yang tersedia.\n";
            continue;
        }
    }

    $kodeBooking = Registration::generateKodeBooking();

    try {
        Registration::create([
            'patient_id'         => $patient->id,
            'department_id'      => $jadwal->department_id,
            'doctor_id'          => $jadwal->doctor_id,
            'doctor_schedule_id' => $jadwal->id,
            'tanggal_daftar'     => $today,
            'tanggal_kunjungan'  => $today,
            'nomor_antrian'      => Registration::generateNomorAntrian($jadwal->department_id, $today),
            'kode_booking'       => $kodeBooking,
            'created_by'         => $petugas ? $petugas->id : null,
        ]);
        $created++;
        echo "  [$i] OK: {$patient->nama_pasien} | Booking: {$kodeBooking} | {$jadwal->doctor->nama_dokter} | {$jadwal->department->nama_poli}\n";
    } catch (\Exception $e) {
        echo "  [$i] GAGAL: {$patient->nama_pasien} - {$e->getMessage()}\n";
    }
}

echo "\n=== Selesai: $created dari 10 pendaftaran berhasil dibuat ===\n";
