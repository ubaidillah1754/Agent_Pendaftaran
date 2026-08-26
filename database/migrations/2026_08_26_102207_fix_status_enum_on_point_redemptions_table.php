<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Perbaiki kolom status di tabel point_redemptions.
     *
     * Sebelumnya ENUM lama: ('pending','disetujui','selesai','ditolak')
     * Seharusnya           : ('pending','approved','completed','rejected','cancelled')
     *
     * Migrasi data lama:
     *   disetujui  → approved
     *   selesai    → completed
     *   ditolak    → rejected
     */
    public function up(): void
    {
        // 1. Ubah ke VARCHAR dulu agar bisa menampung semua nilai sementara
        DB::statement("ALTER TABLE point_redemptions MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending'");

        // 2. Konversi nilai lama ke nilai baru
        DB::table('point_redemptions')->where('status', 'disetujui')->update(['status' => 'approved']);
        DB::table('point_redemptions')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('point_redemptions')->where('status', 'ditolak')->update(['status' => 'rejected']);

        // 3. Ubah ke ENUM baru yang lengkap
        DB::statement("ALTER TABLE point_redemptions MODIFY COLUMN status ENUM('pending','approved','completed','rejected','cancelled') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        // Kembalikan ke VARCHAR dulu
        DB::statement("ALTER TABLE point_redemptions MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'pending'");

        // Kembalikan nilai ke Bahasa Indonesia lama
        DB::table('point_redemptions')->where('status', 'approved')->update(['status' => 'disetujui']);
        DB::table('point_redemptions')->where('status', 'completed')->update(['status' => 'selesai']);
        DB::table('point_redemptions')->where('status', 'rejected')->update(['status' => 'ditolak']);
        DB::table('point_redemptions')->where('status', 'cancelled')->update(['status' => 'pending']); // fallback

        // Kembalikan ENUM lama
        DB::statement("ALTER TABLE point_redemptions MODIFY COLUMN status ENUM('pending','disetujui','selesai','ditolak') NOT NULL DEFAULT 'selesai'");
    }
};
