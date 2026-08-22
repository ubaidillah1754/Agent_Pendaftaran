<?php
use Illuminate\Support\Str;
use App\Models\Registration;

$registrations = Registration::whereNull('kode_booking')->get();
$count = 0;
foreach ($registrations as $r) {
    do {
        $kode = strtoupper(Str::random(6));
    } while (Registration::where('kode_booking', $kode)->exists());
    $r->update(['kode_booking' => $kode]);
    $count++;
}
echo "Updated $count records.\n";
