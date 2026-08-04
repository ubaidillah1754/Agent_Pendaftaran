<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    public function definition(): array
    {
        // Data poli nyata yang sering ada di rumah sakit/klinik
        $poliData = [
            ['kode' => 'PU', 'nama' => 'Poli Umum', 'desk' => 'Pelayanan kesehatan umum untuk semua usia'],
            ['kode' => 'GG', 'nama' => 'Poli Gigi', 'desk' => 'Perawatan dan pengobatan gigi dan mulut'],
            ['kode' => 'AN', 'nama' => 'Poli Anak', 'desk' => 'Pelayanan kesehatan khusus anak (pediatri)'],
            ['kode' => 'KD', 'nama' => 'Poli Kandungan', 'desk' => 'Layanan kebidanan dan kandungan (obstetri & ginekologi)'],
            ['kode' => 'JP', 'nama' => 'Poli Jantung', 'desk' => 'Pemeriksaan dan pengobatan penyakit jantung'],
            ['kode' => 'MT', 'nama' => 'Poli Mata', 'desk' => 'Pemeriksaan dan pengobatan gangguan mata'],
            ['kode' => 'KL', 'nama' => 'Poli Kulit', 'desk' => 'Perawatan penyakit kulit dan kelamin'],
            ['kode' => 'OR', 'nama' => 'Poli Ortopedi', 'desk' => 'Penanganan masalah tulang, sendi, dan otot'],
        ];

        static $index = 0;
        $poli = $poliData[$index % count($poliData)];
        $index++;

        return [
            'kode_poli'  => $poli['kode'],
            'nama_poli'  => $poli['nama'],
            'deskripsi'  => $poli['desk'],
            'is_active'  => true,
        ];
    }
}
