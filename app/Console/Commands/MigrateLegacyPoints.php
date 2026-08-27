<?php

namespace App\Console\Commands;

use App\Models\PetugasPoint;
use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateLegacyPoints extends Command
{
    protected $signature   = 'points:migrate-legacy';
    protected $description = 'Migrasi data poin lama (petugas_points) ke sistem baru (point_transactions + user.point_balance)';

    public function handle(): int
    {
        $legacyPoints = PetugasPoint::all();

        if ($legacyPoints->isEmpty()) {
            $this->info('Tidak ada data poin lama yang perlu dimigrasi.');
            return 0;
        }

        $this->info("Ditemukan {$legacyPoints->count()} record di petugas_points. Memulai migrasi...");

        $migrated = 0;
        $skipped  = 0;

        DB::transaction(function () use ($legacyPoints, &$migrated, &$skipped) {
            foreach ($legacyPoints as $pp) {
                $reference = 'EARN-LEGACY-PP-' . $pp->id;

                // Cek idempotency — skip jika sudah pernah dimigrasikan
                if (PointTransaction::where('reference', $reference)->exists()) {
                    $skipped++;
                    continue;
                }

                $user = User::find($pp->user_id);
                if (!$user) {
                    $this->warn("  -> Skip: user_id={$pp->user_id} tidak ditemukan.");
                    $skipped++;
                    continue;
                }

                // Hitung saldo sebelum & sesudah
                $balanceBefore = (int) $user->point_balance;
                $balanceAfter  = $balanceBefore + (int) $pp->points;

                // Buat entry di point_transactions
                PointTransaction::create([
                    'user_id'        => $user->id,
                    'type'           => 'earn',
                    'amount'         => (int) $pp->points,
                    'balance_before' => $balanceBefore,
                    'balance_after'  => $balanceAfter,
                    'source_type'    => PetugasPoint::class,
                    'source_id'      => $pp->id,
                    'reference'      => $reference,
                    'description'    => 'Migrasi poin lama (petugas_points #' . $pp->id . ')' .
                                        ($pp->registration_id ? " -- Pendaftaran #{$pp->registration_id}" : ''),
                    'created_by'     => $user->id,
                ]);

                // Update saldo user
                $user->point_balance = $balanceAfter;
                $user->save();

                $this->line("  OK user={$user->name} | +{$pp->points} poin | saldo: {$balanceBefore} -> {$balanceAfter}");
                $migrated++;
            }
        });

        $this->newLine();
        $this->info("Selesai! Berhasil: {$migrated}, Dilewati: {$skipped}");

        return 0;
    }
}
