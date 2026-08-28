<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Patient;
use App\Models\DoctorSchedule;
use App\Models\Registration;
use App\Models\User;

class GenerateDemoData extends Command
{
    protected $signature   = 'demo:generate {--poli=5 : Jumlah pasien per poli} {--force : Hapus data demo hari ini sebelum generate ulang}';
    protected $description = 'Generate data pasien + pendaftaran demo untuk semua poli yang punya jadwal hari ini';

    // ─── Data nama pasien dummy (Indonesia realistis) ─────────────────────────

    private array $namaLaki = [
        'Ahmad Fauzi', 'Budi Santoso', 'Candra Wijaya', 'Dede Kurniawan', 'Eko Prasetyo',
        'Fajar Nugroho', 'Galih Permana', 'Hendra Kusuma', 'Irwan Saputra', 'Joko Purnomo',
        'Kevin Andrean', 'Lutfi Hakim', 'Muhammad Rizki', 'Nando Saputra', 'Oscar Firmansyah',
        'Pandu Setiawan', 'Raka Wijayanto', 'Suryo Nugroho', 'Teguh Wibowo', 'Umar Farouk',
        'Wahyu Prasetya', 'Yusuf Hidayat', 'Zaenal Abidin', 'Agus Salim', 'Bambang Sudiro',
        'Dian Pratama', 'Fikri Maulana', 'Gilang Ramadhan', 'Hamzah Ardian', 'Ivan Kusuma',
        'Jafar Siddiq', 'Karim Ridwan', 'Luthfi Anshari', 'Mansur Hakim', 'Naufal Pratama',
        'Panji Wicaksono', 'Ridho Ilahi', 'Sandi Kurniawan', 'Taufik Hidayat', 'Vino Hartanto',
    ];

    private array $namaPerempuan = [
        'Anisa Rahayu', 'Bunga Melati', 'Citra Dewi', 'Desi Fitriani', 'Endah Lestari',
        'Fitri Handayani', 'Gita Puspita', 'Hani Pratiwi', 'Indah Permata', 'Julia Anggraini',
        'Kartika Sari', 'Lilis Suryani', 'Maya Safitri', 'Nadia Kusuma', 'Okta Wulandari',
        'Putri Nurhaliza', 'Rina Susanti', 'Siti Aisyah', 'Tika Rahmawati', 'Ulfa Damayanti',
        'Vika Setyawati', 'Winda Maharani', 'Yuni Astuti', 'Zahra Nabilah', 'Amel Fitriana',
        'Bella Anggraeni', 'Dewi Ratnasar', 'Erni Wahyuni', 'Fara Nabila', 'Halimah Tusadiyah',
        'Intan Permani', 'Jasmine Putri', 'Kirana Dewi', 'Liana Safira', 'Melani Putri',
        'Nisa Aulia', 'Prita Cahyaningrum', 'Reni Agustina', 'Sri Wahyuni', 'Titin Sumarni',
    ];

    private array $tempatLahir = [
        'Jakarta', 'Bandung', 'Surabaya', 'Yogyakarta', 'Semarang',
        'Medan', 'Makassar', 'Palembang', 'Malang', 'Bogor',
        'Bekasi', 'Depok', 'Tangerang', 'Cirebon', 'Balikpapan',
        'Samarinda', 'Manado', 'Padang', 'Pekanbaru', 'Banjarmasin',
    ];

    // ─── Helper: generate NIK unik 16 digit ──────────────────────────────────

    private function generateNIK(): ?string
    {
        for ($try = 0; $try < 30; $try++) {
            $nik = str_pad(rand(11, 94), 2, '0', STR_PAD_LEFT)   // provinsi
                 . str_pad(rand(1, 99), 2, '0', STR_PAD_LEFT)    // kota
                 . str_pad(rand(1, 20), 2, '0', STR_PAD_LEFT)    // kecamatan
                 . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT)    // tgl lahir
                 . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT)    // bulan
                 . rand(60, 99)                                   // tahun (2 digit)
                 . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT); // seq
            if (!DB::table('patients')->where('nik', $nik)->exists()) {
                return $nik;
            }
        }
        return null;
    }

    // ─── Helper: generate no_rm unik ─────────────────────────────────────────

    private function generateNoRM(string $today): string
    {
        $datePart = str_replace('-', '', $today);
        do {
            $seq  = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $noRM = "RM-{$datePart}-{$seq}";
        } while (DB::table('patients')->where('no_rm', $noRM)->exists());
        return $noRM;
    }

    // ─── Helper: generate no telepon ─────────────────────────────────────────

    private function generateNoTelp(): string
    {
        $prefix = ['0812', '0813', '0821', '0822', '0851', '0852', '0856', '0857', '0858'];
        return $prefix[array_rand($prefix)] . str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
    }

    // ─── Main ─────────────────────────────────────────────────────────────────

    public function handle(): int
    {
        $today     = now()->toDateString();
        $jumlah    = (int) $this->option('poli');
        $hariMap   = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis',
            'Friday' => 'Jumat', 'Saturday' => 'Sabtu',
        ];
        $hariIni = $hariMap[now()->format('l')];

        $this->newLine();
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║   GENERATE DEMO DATA — PASIEN BARU PER POLI          ║');
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->line("  Tanggal : <info>{$today}</info>  |  Hari : <info>{$hariIni}</info>");
        $this->line("  Target  : <info>{$jumlah} pasien baru</info> per poli");
        $this->newLine();

        // ── Jadwal hari ini ───────────────────────────────────────────────────
        $jadwalHariIni = DoctorSchedule::with(['doctor', 'department'])
            ->where('hari', $hariIni)
            ->get();

        if ($jadwalHariIni->isEmpty()) {
            $this->error("Tidak ada jadwal dokter untuk hari {$hariIni}.");
            $this->line('Jadwal yang tersedia:');
            DoctorSchedule::with(['doctor', 'department'])->get()->each(function ($s) {
                $this->line("  - {$s->hari}: {$s->doctor->nama_dokter} ({$s->department->nama_poli})");
            });
            return self::FAILURE;
        }

        // ── Kelompokkan per poli ──────────────────────────────────────────────
        $jadwalPerPoli = $jadwalHariIni->groupBy('department_id');

        $this->line('  Jadwal ditemukan: <info>' . $jadwalPerPoli->count() . ' poli</info>');
        foreach ($jadwalPerPoli as $depId => $jadwals) {
            $j = $jadwals->first();
            $this->line("    [{$depId}] {$j->department->nama_poli} — Dr. {$j->doctor->nama_dokter} ({$j->jam_mulai}–{$j->jam_selesai})");
        }

        // ── Petugas ───────────────────────────────────────────────────────────
        $petugas = User::whereIn('role', ['petugas', 'admin'])->first() ?? User::first();
        if (!$petugas) {
            $this->error('Tidak ada user petugas/admin di database.');
            return self::FAILURE;
        }
        $this->line("\n  Petugas  : <info>{$petugas->name}</info> (ID: {$petugas->id})");
        $this->newLine();

        // ── Data pools ────────────────────────────────────────────────────────
        $golonganDarah    = ['A', 'B', 'AB', 'O', 'Tidak Diketahui'];
        $jenisPembayaran  = ['umum', 'bpjs', 'umum', 'bpjs', 'asuransi'];

        $totalPasien     = 0;
        $totalPendaftar  = 0;

        // ── Loop per poli ─────────────────────────────────────────────────────
        foreach ($jadwalPerPoli as $departmentId => $jadwals) {
            $jadwal    = $jadwals->first();
            $namaPoli  = $jadwal->department->nama_poli;

            $this->line("───────────────────────────────────────────────────────");
            $this->line("  POLI   : <comment>{$namaPoli}</comment>");
            $this->line("  DOKTER : Dr. {$jadwal->doctor->nama_dokter}  |  {$jadwal->jam_mulai}–{$jadwal->jam_selesai}");
            $this->line("───────────────────────────────────────────────────────");

            $berhasilPoli = 0;
            $usedNames    = [];

            for ($i = 1; $i <= $jumlah; $i++) {
                // Pilih nama (hindari duplikasi dalam satu loop poli)
                $isLaki = rand(0, 1);
                $pool   = $isLaki ? $this->namaLaki : $this->namaPerempuan;
                $jenisKelamin = $isLaki ? 'L' : 'P';

                $baseName = null;
                for ($t = 0; $t < 20; $t++) {
                    $candidate = $pool[array_rand($pool)];
                    if (!in_array($candidate, $usedNames, true)) {
                        $baseName = $candidate;
                        $usedNames[] = $baseName;
                        break;
                    }
                }
                if (!$baseName) {
                    $baseName = ($isLaki ? 'Pasien Laki' : 'Pasien Perempuan') . " Demo-{$i}";
                }

                // Tambah suffix agar nama unik di db
                $suffix     = '-' . strtoupper(substr(md5(uniqid()), 0, 4));
                $namaPasien = $baseName . $suffix;

                // NIK unik
                $nik = $this->generateNIK();
                if (!$nik) {
                    $this->warn("  [{$i}] SKIP: Gagal generate NIK unik.");
                    continue;
                }

                $noRM       = $this->generateNoRM($today);
                $tglLahir   = date('Y-m-d', mktime(0, 0, 0, rand(1, 12), rand(1, 28), rand(1960, 2005)));
                $jenisBayar = $jenisPembayaran[array_rand($jenisPembayaran)];
                $noBPJS     = ($jenisBayar === 'bpjs') ? '000' . rand(100000000000, 999999999999) : null;
                $noAsuransi = ($jenisBayar === 'asuransi') ? 'ASR-' . rand(100000, 999999) : null;
                $tempat     = $this->tempatLahir[array_rand($this->tempatLahir)];

                try {
                    DB::beginTransaction();

                    // Buat pasien baru
                    $patient = Patient::create([
                        'no_rm'            => $noRM,
                        'nik'              => $nik,
                        'nama_pasien'      => $namaPasien,
                        'jenis_kelamin'    => $jenisKelamin,
                        'tempat_lahir'     => $tempat,
                        'tanggal_lahir'    => $tglLahir,
                        'alamat'           => 'Jl. Demo No. ' . rand(1, 200) . ', ' . $tempat,
                        'no_telepon'       => $this->generateNoTelp(),
                        'golongan_darah'   => $golonganDarah[array_rand($golonganDarah)],
                        'jenis_pembayaran' => $jenisBayar,
                        'no_bpjs'          => $noBPJS,
                        'no_asuransi'      => $noAsuransi,
                        'created_by'       => $petugas->id,
                    ]);

                    // Buat registrasi (walk-in — langsung ambil nomor antrian)
                    $kodeBooking  = Registration::generateKodeBooking();
                    $nomorAntrian = Registration::generateNomorAntrian($departmentId, $today);

                    Registration::create([
                        'patient_id'         => $patient->id,
                        'department_id'      => $departmentId,
                        'doctor_id'          => $jadwal->doctor_id,
                        'doctor_schedule_id' => $jadwal->id,
                        'tanggal_daftar'     => $today,
                        'tanggal_kunjungan'  => $today,
                        'kode_booking'       => $kodeBooking,
                        'nomor_antrian'      => $nomorAntrian,
                        'created_by'         => $petugas->id,
                    ]);

                    DB::commit();

                    $badge = $jenisBayar === 'bpjs' ? '🟦' : ($jenisBayar === 'asuransi' ? '🟩' : '⬜');
                    $this->line("  [<info>{$i}</info>] ✓ {$namaPasien} | {$jenisKelamin} | {$badge} {$jenisBayar} | Antrian: <comment>{$nomorAntrian}</comment> | {$kodeBooking}");
                    $berhasilPoli++;
                    $totalPasien++;
                    $totalPendaftar++;

                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->warn("  [{$i}] ✗ GAGAL: " . $e->getMessage());
                }
            }

            $this->line("  → <info>{$berhasilPoli}/{$jumlah}</info> pendaftaran berhasil untuk poli <comment>{$namaPoli}</comment>");
            $this->newLine();
        }

        // ── Ringkasan akhir ───────────────────────────────────────────────────
        $this->line('╔══════════════════════════════════════════════════════╗');
        $this->line('║  SELESAI                                             ║');
        $this->line('╠══════════════════════════════════════════════════════╣');
        $this->line("║  Pasien baru dibuat    : <info>{$totalPasien}</info>");
        $this->line("║  Pendaftaran dibuat    : <info>{$totalPendaftar}</info>");
        $this->line("║  Poli yang dilayani    : <info>{$jadwalPerPoli->count()}</info>");
        $this->line('╚══════════════════════════════════════════════════════╝');
        $this->newLine();

        return self::SUCCESS;
    }
}
