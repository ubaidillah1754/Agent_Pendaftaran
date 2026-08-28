<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            // Re-create them using tanggal_kunjungan first so MySQL can use them for the foreign keys
            $table->unique(['patient_id', 'department_id', 'tanggal_kunjungan'], 'unique_kunjungan_harian');
            $table->unique(['department_id', 'tanggal_kunjungan', 'nomor_antrian'], 'unique_antrian_kunjungan');

            // Drop old indexes/constraints that use tanggal_daftar
            $table->dropUnique('unique_pendaftaran_harian');
            $table->dropUnique('unique_antrian_harian');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropUnique('unique_kunjungan_harian');
            $table->dropUnique('unique_antrian_kunjungan');

            $table->unique(['patient_id', 'department_id', 'tanggal_daftar'], 'unique_pendaftaran_harian');
            $table->unique(['department_id', 'tanggal_daftar', 'nomor_antrian'], 'unique_antrian_harian');
        });
    }
};
