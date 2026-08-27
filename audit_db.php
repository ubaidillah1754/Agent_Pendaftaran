<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== COLUMNS IN registrations ===\n";
$cols = Schema::getColumnListing('registrations');
foreach ($cols as $col) {
    echo "  - $col\n";
}

echo "\n=== INDEXES IN registrations ===\n";
$indexes = DB::select('SHOW INDEX FROM registrations');
foreach ($indexes as $idx) {
    $unique = !$idx->Non_unique ? 'UNIQUE' : '      ';
    echo "  $unique | {$idx->Key_name} | col={$idx->Column_name}\n";
}

echo "\n=== TOTAL REGISTRATIONS ===\n";
$total = DB::table('registrations')->count();
echo "  Total: $total\n";

echo "\n=== DUPLICATE kode_booking ===\n";
$duplikat = DB::table('registrations')
    ->select('kode_booking', DB::raw('COUNT(*) as total'))
    ->whereNotNull('kode_booking')
    ->groupBy('kode_booking')
    ->having('total', '>', 1)
    ->get();
if ($duplikat->isEmpty()) {
    echo "  None found.\n";
} else {
    foreach ($duplikat as $d) {
        echo "  kode={$d->kode_booking} (x{$d->total})\n";
    }
}

echo "\n=== NULL kode_booking ===\n";
$nullCount = DB::table('registrations')->whereNull('kode_booking')->count();
echo "  NULL count: $nullCount\n";

echo "\n=== SAMPLE REGISTRATIONS ===\n";
$samples = DB::table('registrations')->limit(5)->get(['id', 'kode_booking', 'status', 'tanggal_kunjungan', 'doctor_id', 'doctor_schedule_id']);
foreach ($samples as $s) {
    echo "  id={$s->id} kode={$s->kode_booking} status={$s->status} tgl_kunjungan={$s->tanggal_kunjungan}\n";
}
