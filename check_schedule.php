<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->boot();

$hariMap = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$hariIni = $hariMap[now()->format('l')];
echo 'Hari ini: ' . $hariIni . PHP_EOL;

$jadwals = App\Models\DoctorSchedule::with(['doctor','department'])->where('hari',$hariIni)->get();
foreach($jadwals as $j) {
    echo '  Poli: ' . $j->department->nama_poli . ' | Dokter: ' . $j->doctor->nama_dokter . ' | ID: ' . $j->id . PHP_EOL;
}
echo 'Total jadwal: ' . $jadwals->count() . PHP_EOL;
