<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Migrasi lengkap untuk sistem kode booking & antrean baru.
     *
     * Yang dilakukan:
     * 1. Drop FK department_id (sementara) agar unique_antrian_kunjungan bisa di-drop
     * 2. Drop unique_antrian_kunjungan
     * 3. Restore FK department_id
     * 4. Drop kolom nomor_antrian lama & urutan_antrian
     * 5. Tambah kolom status_booking & nomor_antrian baru
     * 6. Generate kode_booking BK-XXXX untuk data NULL
     * 7. Tambah UNIQUE index pada kode_booking
     * 8. Tambah UNIQUE composite [department_id, tanggal_kunjungan, nomor_antrian]
     * 9. Rename status dipanggil → diperiksa
     */
    public function up(): void
    {
        // ── STEP 1: Sementara drop FK department_id agar bisa drop unique index ──
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropForeign('registrations_department_id_foreign');
            });
        }

        // ── STEP 2: Drop unique index yang memakai nomor_antrian lama ─────────
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropUnique('unique_antrian_kunjungan');
            });
        }

        // ── STEP 3: Restore FK department_id ─────────────────────────────────
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('registrations', function (Blueprint $table) {
                $table->foreign('department_id')->references('id')->on('departments');
            });
        }

        // ── STEP 4: Drop kolom lama ───────────────────────────────────────────
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            Schema::table('registrations', function (Blueprint $table) {
                $table->dropColumn(['nomor_antrian', 'urutan_antrian']);
            });
        }

        // ── STEP 5: Tambah kolom baru ─────────────────────────────────────────
        Schema::table('registrations', function (Blueprint $table) {
            // Status booking lifecycle: pending → used/expired/cancelled
            $table->enum('status_booking', ['pending', 'used', 'expired', 'cancelled'])
                ->default('pending')
                ->after('kode_booking');

            // Nomor antrean baru (format A01, nullable sampai pasien mengambil antrean)
            $table->string('nomor_antrian', 5)->nullable()->after('status_booking');
        });

        // ── STEP 6: Generate kode_booking BK-XXXX untuk data NULL ────────────
        $nullBookings = DB::table('registrations')
            ->where(function ($q) {
                $q->whereNull('kode_booking')->orWhere('kode_booking', '');
            })
            ->get(['id']);

        foreach ($nullBookings as $reg) {
            do {
                // Karakter yang tidak membingungkan (tanpa 0,1,I,O)
                $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                $suffix = '';
                for ($i = 0; $i < 4; $i++) {
                    $suffix .= $chars[random_int(0, strlen($chars) - 1)];
                }
                $kode = 'BK-' . $suffix;
            } while (DB::table('registrations')->where('kode_booking', $kode)->exists());

            DB::table('registrations')->where('id', $reg->id)->update(['kode_booking' => $kode]);
        }

        // ── STEP 7: Set status_booking sesuai status registrasi existing ──────
        // Yang sudah selesai/diperiksa → used
        DB::table('registrations')
            ->whereIn('status', ['selesai', 'dipanggil'])
            ->update(['status_booking' => 'used']);

        // Yang batal → cancelled
        DB::table('registrations')
            ->where('status', 'batal')
            ->update(['status_booking' => 'cancelled']);

        // ── STEP 8: Tambah UNIQUE index pada kode_booking ────────────────────
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique('kode_booking', 'unique_kode_booking');
        });

        // ── STEP 9: UNIQUE composite nomor_antrian (NULL-safe di MySQL) ───────
        Schema::table('registrations', function (Blueprint $table) {
            $table->unique(
                ['department_id', 'tanggal_kunjungan', 'nomor_antrian'],
                'unique_nomor_antrian_harian'
            );
        });

        // ── STEP 10: Rename status dipanggil → diperiksa ─────────────────────
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE registrations MODIFY COLUMN `status` ENUM('menunggu','diperiksa','selesai','batal') NOT NULL DEFAULT 'menunggu'"
            );
        }
        DB::table('registrations')
            ->where('status', 'dipanggil')
            ->update(['status' => 'diperiksa']);
    }

    public function down(): void
    {
        // Rollback status diperiksa → dipanggil
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE registrations MODIFY COLUMN `status` ENUM('menunggu','dipanggil','diperiksa','selesai','batal') NOT NULL DEFAULT 'menunggu'"
            );
        }
        DB::table('registrations')->where('status', 'diperiksa')->update(['status' => 'dipanggil']);
        if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
            DB::statement(
                "ALTER TABLE registrations MODIFY COLUMN `status` ENUM('menunggu','dipanggil','selesai','batal') NOT NULL DEFAULT 'menunggu'"
            );
        }

        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique('unique_nomor_antrian_harian');
            $table->dropUnique('unique_kode_booking');
            $table->dropColumn(['status_booking', 'nomor_antrian']);
        });

        Schema::table('registrations', function (Blueprint $table) {
            $table->string('nomor_antrian', 10)->after('tanggal_kunjungan');
            $table->unsignedSmallInteger('urutan_antrian')->default(0)->after('nomor_antrian');
        });

        Schema::table('registrations', function (Blueprint $table) {
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['department_id']);
                $table->unique(
                    ['department_id', 'tanggal_kunjungan', 'nomor_antrian'],
                    'unique_antrian_kunjungan'
                );
                $table->foreign('department_id')->references('id')->on('departments');
            }
        });
    }
};
