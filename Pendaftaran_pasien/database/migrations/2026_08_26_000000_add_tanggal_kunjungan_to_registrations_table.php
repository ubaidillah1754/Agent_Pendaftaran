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
            // Menambahkan kolom tanggal_kunjungan setelah tanggal_daftar
            $table->date('tanggal_kunjungan')->nullable()->after('tanggal_daftar');
        });

        // Copy existing data from tanggal_daftar to tanggal_kunjungan so old data is not lost
        \Illuminate\Support\Facades\DB::table('registrations')->update([
            'tanggal_kunjungan' => \Illuminate\Support\Facades\DB::raw('tanggal_daftar')
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registrations', function (Blueprint $table) {
            $table->dropColumn('tanggal_kunjungan');
        });
    }
};
