<?php

namespace Database\Seeders;

use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoTodaySeeder extends Seeder
{
    public function run(): void
    {
        $today   = now()->toDateString();
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

        $this->command->info("Tanggal: $today | Hari: $hariIni");

        // Ambil jadwal hari ini
        $jadwalList = DoctorSchedule::with(['doctor', 'department'])
            ->where('hari', $hariIni)
            ->get();

        if ($jadwalList->isEmpty()) {
            $this->command->error("Tidak ada jadwal untuk hari $hariIni!");
            $this->command->info("Jadwal tersedia:");
            DoctorSchedule::with(['doctor', 'department'])->get()->each(function ($s) {
                $this->command->line("  - {$s->hari}: {$s->doctor->nama_dokter} ({$s->department->nama_poli})");
            });
            return;
        }

        $this->command->info("Jadwal hari ini:");
        foreach ($jadwalList as $j) {
            $this->command->line("  [{$j->id}] {$j->doctor->nama_dokter} ({$j->department->nama_poli}) {$j->jam_mulai}-{$j->jam_selesai}");
        }

        // Ambil semua pasien
        $patients = Patient::orderBy('id')->get();
        if ($patients->isEmpty()) {
            $this->command->error('Tidak ada data pasien!');
            return;
        }

        $petugas  = User::first();
        $created          = 0;
        $usedCombinations = [];

        // Pilih jadwal pertama saja untuk ke 10 pendaftaran agar antrean mengular
        $jadwal = $jadwalList->first();
        $this->command->info("Membuat 10 pendaftaran untuk Poli: {$jadwal->department->nama_poli} (Dokter: {$jadwal->doctor->nama_dokter})");

        for ($i = 0; $i < 10; $i++) {
            // Cari pasien yang belum terdaftar di jadwal ini hari ini
            $patient = null;
            foreach ($patients as $p) {
                $key = "{$p->id}-{$jadwal->id}";
                if (in_array($key, $usedCombinations)) {
                    continue;
                }
                $exists = Registration::where('patient_id', $p->id)
                    ->where('doctor_schedule_id', $jadwal->id)
                    ->whereDate('tanggal_kunjungan', $today)
                    ->exists();
                if (!$exists) {
                    $patient          = $p;
                    $usedCombinations[] = $key;
                    break;
                }
            }

            if (!$patient) {
                $this->command->warn("  [$i] SKIP: tidak ada pasien tersisa untuk jadwal ID {$jadwal->id}");
                continue;
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
                    'status'             => 'menunggu',
                    'status_booking'     => 'pending',
                    'kode_booking'       => $kodeBooking,
                    'nomor_antrian'      => null,
                    'created_by'         => $petugas?->id,
                ]);
                $created++;
                $this->command->line(
                    "  [$i] <fg=green>OK</> {$patient->nama_pasien} | " .
                    "<fg=cyan>{$kodeBooking}</> | " .
                    "{$jadwal->doctor->nama_dokter} | {$jadwal->department->nama_poli}"
                );
            } catch (\Exception $e) {
                $this->command->error("  [$i] GAGAL: {$patient->nama_pasien} — {$e->getMessage()}");
            }
        }

        $this->command->newLine();
        $this->command->info("=== Selesai: $created dari 10 pendaftaran berhasil dibuat ===");
        $this->command->info("Gunakan kode booking di atas untuk demo di /cek-pendaftaran");
    }
}

