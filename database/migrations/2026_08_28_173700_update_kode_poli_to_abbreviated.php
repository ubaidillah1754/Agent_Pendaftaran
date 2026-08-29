<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $departments = DB::table('departments')
            ->orderBy('id')
            ->get();

        /*
         * Berikan kode sementara yang unik terlebih dahulu.
         * Ini menghindari bentrok UNIQUE saat kode lama
         * sedang diganti.
         */
        foreach ($departments as $dept) {
            DB::table('departments')
                ->where('id', $dept->id)
                ->update([
                    'kode_poli' => 'TMP' . $dept->id,
                ]);
        }

        $usedCodes = [];

        foreach ($departments as $dept) {
            // Hapus "Poli " dari awal nama poli
            $namaTanpaPoli = trim(
                preg_replace('/^poli\s+/i', '', $dept->nama_poli)
            );

            // Ambil 3 huruf pertama
            $baseCode = strtoupper(substr($namaTanpaPoli, 0, 3));

            // Kalau kosong
            if ($baseCode === '') {
                $baseCode = 'POL';
            }

            $kode = $baseCode;
            $counter = 2;

            // Pastikan kode unik
            while (in_array($kode, $usedCodes, true)) {
                $kode = substr($baseCode, 0, 2) . $counter;
                $counter++;
            }

            $usedCodes[] = $kode;

            DB::table('departments')
                ->where('id', $dept->id)
                ->update([
                    'kode_poli' => $kode,
                ]);
        }
    }

    public function down(): void
    {
        // Tidak ada rollback otomatis
    }
};