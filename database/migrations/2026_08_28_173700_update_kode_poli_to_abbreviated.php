<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Selaraskan kode_poli dengan prefix nomor antrian:
     * ambil 3 huruf pertama nama poli (tanpa kata "Poli ").
     *
     * Contoh:
     *   Poli Gizi      → GIZ
     *   Poli Umum      → UMU
     *   Poli Anak      → ANK  (dari "Anak")
     *   Poli Kandungan → KAN
     *   Poli Jantung   → JAN
     */
    public function up(): void
    {
        $departments = DB::table('departments')->get();

        foreach ($departments as $dept) {
            // Hapus "Poli " dari depan (case-insensitive), ambil 3 huruf pertama, uppercase
            $namaTanpaPoli = trim(preg_replace('/^poli\s+/i', '', $dept->nama_poli));
            $kode = strtoupper(substr($namaTanpaPoli, 0, 3));

            DB::table('departments')
                ->where('id', $dept->id)
                ->update(['kode_poli' => $kode]);
        }
    }

    /**
     * Tidak bisa dikembalikan secara otomatis karena nilai lama tidak disimpan.
     */
    public function down(): void
    {
        // Tidak ada rollback otomatis — perlu diisi manual jika diperlukan
    }
};
