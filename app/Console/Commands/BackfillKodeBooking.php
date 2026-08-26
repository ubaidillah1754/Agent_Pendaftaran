<?php

namespace App\Console\Commands;

use App\Models\Registration;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class BackfillKodeBooking extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'registrations:backfill-kode-booking';

    /**
     * The console command description.
     */
    protected $description = 'Isi kode_booking untuk semua registrasi yang nilai kode_booking-nya NULL';

    /**
     * Execute the console command.
     *
     * NOTE: Format kode booking saat ini menggunakan 6 karakter
     * alphanumeric acak (uppercase). Format ini SEMENTARA dan dapat
     * disesuaikan setelah mendapat konfirmasi format resmi dari pihak RSI.
     * Generator: strtoupper(Str::random(6)) — contoh: "A3BX7Q"
     */
    public function handle()
    {
        $nullRecords = Registration::whereNull('kode_booking')->get();

        if ($nullRecords->isEmpty()) {
            $this->info('Tidak ada data registrasi yang perlu diisi kode booking.');
            return;
        }

        $this->info("Ditemukan {$nullRecords->count()} registrasi dengan kode_booking NULL. Mulai mengisi...");

        $bar = $this->output->createProgressBar($nullRecords->count());
        $bar->start();

        $filled = 0;
        foreach ($nullRecords as $reg) {
            do {
                $kode = strtoupper(Str::random(6));
            } while (Registration::where('kode_booking', $kode)->exists());

            // Gunakan DB update langsung agar tidak memicu event/observer lain
            $reg->timestamps = false;
            $reg->kode_booking = $kode;
            $reg->save();

            $bar->advance();
            $filled++;
        }

        $bar->finish();
        $this->newLine();
        $this->info("Selesai! {$filled} registrasi berhasil diisi kode booking.");
    }
}
