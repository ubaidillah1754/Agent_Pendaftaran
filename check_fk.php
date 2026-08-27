<?php
define('LARAVEL_START', microtime(true));
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Check what is referencing unique_antrian_kunjungan as FK
$fks = DB::select("
    SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = DATABASE()
    AND REFERENCED_TABLE_NAME IS NOT NULL
    AND TABLE_NAME = 'registrations'
");

echo "=== FOREIGN KEYS on registrations ===\n";
foreach ($fks as $fk) {
    echo "  constraint={$fk->CONSTRAINT_NAME} col={$fk->COLUMN_NAME} refs={$fk->REFERENCED_TABLE_NAME}.{$fk->REFERENCED_COLUMN_NAME}\n";
}

// Check constraints
$constraints = DB::select("
    SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE
    FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'registrations'
");

echo "\n=== CONSTRAINTS on registrations ===\n";
foreach ($constraints as $c) {
    echo "  {$c->CONSTRAINT_TYPE} | {$c->CONSTRAINT_NAME}\n";
}
