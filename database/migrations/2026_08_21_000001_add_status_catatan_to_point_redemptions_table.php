<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom status dan catatan ke tabel point_redemptions.
     *
     * status:  alur penukaran poin (pending → disetujui → selesai / ditolak)
     * catatan: keterangan tambahan dari admin (opsional)
     *
     * Data lama (tanpa status) dianggap 'selesai' secara default agar saldo
     * yang sudah terhitung tidak berubah. (backward compatible)
     */
    public function up(): void
    {
        Schema::table('point_redemptions', function (Blueprint $table) {
            // Status alur penukaran poin
            $table->enum('status', ['pending', 'disetujui', 'selesai', 'ditolak'])
                  ->default('selesai')   // data lama dianggap sudah selesai
                  ->after('type');

            // Catatan/keterangan dari admin (opsional)
            $table->text('catatan')->nullable()->after('status');
        });
    }

    /**
     * Rollback: hapus kolom yang baru ditambahkan.
     */
    public function down(): void
    {
        Schema::table('point_redemptions', function (Blueprint $table) {
            $table->dropColumn(['status', 'catatan']);
        });
    }
};
