<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed database dengan data dummy untuk testing.
     * Urutan seeding penting karena ada foreign key constraint.
     */
    public function run(): void
    {
        // ── 1. Akun Pengguna ────────────────────────────────────────────────

        // Admin utama
        $admin = User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@rsklinik.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // Petugas pendaftaran
        $petugas = User::create([
            'name'     => 'Petugas Pendaftaran',
            'email'    => 'petugas@rsklinik.id',
            'password' => Hash::make('password'),
            'role'     => 'petugas',
        ]);

        $this->command->info('✓ Akun pengguna dibuat');

        // ── 2. Data Poli ────────────────────────────────────────────────────

        $poliList = [
            [
                'kode_poli' => 'UMU',  // Umum → UMU
                'nama_poli' => 'Poli Umum',
                'deskripsi' => 'Pelayanan kesehatan umum untuk semua usia',
            ],
            [
                'kode_poli' => 'GIG',  // Gigi → GIG
                'nama_poli' => 'Poli Gigi',
                'deskripsi' => 'Perawatan dan pengobatan gigi dan mulut',
            ],
            [
                'kode_poli' => 'ANA',  // Anak → ANA
                'nama_poli' => 'Poli Anak',
                'deskripsi' => 'Pelayanan kesehatan khusus anak (pediatri)',
            ],
            [
                'kode_poli' => 'KAN',  // Kandungan → KAN
                'nama_poli' => 'Poli Kandungan',
                'deskripsi' => 'Layanan kebidanan dan kandungan',
            ],
            [
                'kode_poli' => 'JAN',  // Jantung → JAN
                'nama_poli' => 'Poli Jantung',
                'deskripsi' => 'Pemeriksaan dan pengobatan penyakit jantung',
            ],
        ];

        foreach ($poliList as $poli) {
            Department::create(
                array_merge($poli, [
                    'is_active' => true,
                ])
            );
        }

        $this->command->info(
            '✓ Data poli dibuat (' . count($poliList) . ' poli)'
        );

        // ── 3. Data Dokter ──────────────────────────────────────────────────

        $poliUmum = Department::where('kode_poli', 'UMU')->first();
        $poliGigi = Department::where('kode_poli', 'GIG')->first();
        $poliAnak = Department::where('kode_poli', 'ANA')->first();
        $poliKandungan = Department::where('kode_poli', 'KAN')->first();
        $poliJantung = Department::where('kode_poli', 'JAN')->first();

        $dokterList = [
            [
                'department_id' => $poliUmum->id,
                'nip' => '1001',
                'nama_dokter' => 'dr. Budi Santoso',
                'spesialisasi' => 'Dokter Umum',
                'no_telepon' => '081234560001',
            ],
            [
                'department_id' => $poliUmum->id,
                'nip' => '1002',
                'nama_dokter' => 'dr. Siti Rahayu',
                'spesialisasi' => 'Dokter Umum',
                'no_telepon' => '081234560002',
            ],
            [
                'department_id' => $poliGigi->id,
                'nip' => '1003',
                'nama_dokter' => 'drg. Ahmad Fauzi',
                'spesialisasi' => 'Spesialis Gigi',
                'no_telepon' => '081234560003',
            ],
            [
                'department_id' => $poliAnak->id,
                'nip' => '1004',
                'nama_dokter' => 'dr. Dewi Kurniawati, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'no_telepon' => '081234560004',
            ],
            [
                'department_id' => $poliKandungan->id,
                'nip' => '1005',
                'nama_dokter' => 'dr. Rina Wijaya, Sp.OG',
                'spesialisasi' => 'Spesialis Kandungan',
                'no_telepon' => '081234560005',
            ],
            [
                'department_id' => $poliJantung->id,
                'nip' => '1006',
                'nama_dokter' => 'dr. Hendra Gunawan, Sp.JP',
                'spesialisasi' => 'Spesialis Jantung',
                'no_telepon' => '081234560006',
            ],
        ];

        foreach ($dokterList as $dokter) {
            Doctor::create(
                array_merge($dokter, [
                    'is_active' => true,
                ])
            );
        }

        $this->command->info(
            '✓ Data dokter dibuat (' . count($dokterList) . ' dokter)'
        );

        // ── 4. Jadwal Praktik ───────────────────────────────────────────────

        $doctors = Doctor::with('department')->get();
        $jadwalData = [];

        foreach ($doctors as $dokter) {
            // Setiap dokter mendapat 3 hari jadwal praktik
            $hariList = collect([
                'Senin',
                'Selasa',
                'Rabu',
                'Kamis',
                'Jumat',
            ])
                ->shuffle()
                ->take(3)
                ->values();

            foreach ($hariList as $hari) {
                $jadwalData[] = [
                    'doctor_id' => $dokter->id,
                    'department_id' => $dokter->department_id,
                    'hari' => $hari,
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '12:00:00',
                    'kuota' => rand(15, 25),
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DoctorSchedule::insert($jadwalData);

        $this->command->info(
            '✓ Jadwal praktik dibuat (' . count($jadwalData) . ' slot)'
        );

        // ── 5. Data Pasien ──────────────────────────────────────────────────

        Patient::factory(30)->create();

        $this->command->info('✓ Data pasien dibuat (30 pasien)');

        // ── 6. Data Pendaftaran (Hari Ini) ──────────────────────────────────

        $patients = Patient::all();
        $schedules = DoctorSchedule::with('department')->get();

        $today = now()->format('Y-m-d');
        $hariIni = DoctorSchedule::hariDariTanggal($today);

        // Ambil jadwal yang sesuai hari ini
        $jadwalHariIni = $schedules
            ->where('hari', $hariIni)
            ->values();

        if ($jadwalHariIni->isNotEmpty()) {
            $registrasi = [];
            $antrianPerPoli = [];

            // Buat 10 pendaftaran sample hari ini
            $samplePatients = $patients->shuffle()->take(10);

            foreach ($samplePatients as $i => $pasien) {
                $jadwal = $jadwalHariIni[$i % $jadwalHariIni->count()];

                $kodePoli = strtoupper(
                    $jadwal->department->kode_poli
                );

                if (!isset($antrianPerPoli[$kodePoli])) {
                    $antrianPerPoli[$kodePoli] = 0;
                }

                $antrianPerPoli[$kodePoli]++;

                $urutan = $antrianPerPoli[$kodePoli];

                $registrasi[] = [
                    'patient_id' => $pasien->id,
                    'doctor_schedule_id' => $jadwal->id,
                    'department_id' => $jadwal->department_id,
                    'doctor_id' => $jadwal->doctor_id,

                    // Tanggal pendaftaran
                    'tanggal_daftar' => $today,

                    // Tanggal kunjungan
                    'tanggal_kunjungan' => $today,

                    // Nomor antrian tetap digunakan
                    'nomor_antrian' => $kodePoli . str_pad(
                        $urutan,
                        3,
                        '0',
                        STR_PAD_LEFT
                    ),

                    // User yang membuat pendaftaran
                    'created_by' => $petugas->id,

                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            Registration::insert($registrasi);

            $this->command->info(
                '✓ Data pendaftaran hari ini dibuat (' .
                count($registrasi) .
                ' pendaftaran)'
            );
        } else {
            $this->command->warn(
                '⚠ Tidak ada jadwal untuk hari ini (' .
                $hariIni .
                '), skip pendaftaran demo'
            );
        }

        // ── Selesai ─────────────────────────────────────────────────────────

        $this->command->newLine();

        $this->command->info(
            '═══════════════════════════════════════'
        );

        $this->command->info(
            '  Seeding selesai! Akun login:'
        );

        $this->command->info(
            '  Admin   : admin@rsklinik.id / password'
        );

        $this->command->info(
            '  Petugas : petugas@rsklinik.id / password'
        );

        $this->command->info(
            '═══════════════════════════════════════'
        );
    }
}