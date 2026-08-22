<?php
use App\Models\PetugasPoint;
use App\Models\Registration;
use App\Models\User;

$user = User::where('role', 'petugas')->first();
$reg = Registration::first();
for ($i = 1; $i <= 15; $i++) {
    PetugasPoint::create([
        'user_id' => $user->id,
        'registration_id' => $reg->id,
        'department_id' => $reg->department_id,
        'points' => 1,
        'created_at' => now()->subDays(rand(1, 28))
    ]);
}
echo 'Data dummy berhasil ditambahkan';
