<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoctorFactory extends Factory
{
    public function definition(): array
    {
        $gelar   = $this->faker->randomElement(['dr.', 'drg.', 'dr. Sp.']);
        $nama    = $this->faker->name('male');
        $spesialis = $this->faker->randomElement([
            'Dokter Umum', 'Spesialis Anak', 'Spesialis Gigi', 'Spesialis Jantung',
            'Spesialis Mata', 'Spesialis Kandungan', 'Spesialis Kulit', 'Spesialis Ortopedi',
        ]);

        return [
            'user_id'       => null, // Opsional — bisa di-set manual jika dokter punya akun
            'department_id' => Department::inRandomOrder()->value('id') ?? Department::factory(),
            'nip'           => $this->faker->unique()->numerify('##########'),
            'nama_dokter'   => $gelar . ' ' . $nama,
            'spesialisasi'  => $spesialis,
            'no_telepon'    => '08' . $this->faker->numerify('#########'),
            'foto'          => null,
            'is_active'     => true,
        ];
    }
}
