<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

class PatientFactory extends Factory
{
    public function definition(): array
    {
        $jk         = $this->faker->randomElement(['L', 'P']);
        $gender     = $jk === 'L' ? 'male' : 'female';
        $tglLahir   = $this->faker->dateTimeBetween('-70 years', '-5 years')->format('Y-m-d');
        $today      = now()->format('Ymd');

        // Generate No. RM unik
        static $rmCounter = 1;
        $noRM = 'RM-' . $today . '-' . str_pad($rmCounter++, 4, '0', STR_PAD_LEFT);

        $pembayaran = $this->faker->randomElement(['umum', 'bpjs', 'asuransi']);

        return [
            'no_rm'             => $noRM,
            'nik'               => $this->faker->unique()->numerify('################'), // 16 digit
            'nama_pasien'       => $this->faker->name($gender),
            'jenis_kelamin'     => $jk,
            'tempat_lahir'      => $this->faker->city(),
            'tanggal_lahir'     => $tglLahir,
            'alamat'            => $this->faker->address(),
            'no_telepon'        => '08' . $this->faker->numerify('#########'),
            'nama_wali'         => $this->faker->name(),
            'no_telepon_wali'   => '08' . $this->faker->numerify('#########'),
            'golongan_darah'    => $this->faker->randomElement(['A', 'B', 'AB', 'O', 'Tidak Diketahui']),
            'jenis_pembayaran'  => $pembayaran,
            'no_bpjs'           => $pembayaran === 'bpjs' ? $this->faker->numerify('##############') : null,
            'no_asuransi'       => $pembayaran === 'asuransi' ? $this->faker->numerify('AS#########') : null,
        ];
    }
}
