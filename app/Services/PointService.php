<?php

namespace App\Services;

use App\Models\PointTransaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class PointService
{
    public function __construct(
        protected AuditService $auditService
    ) {}

    /**
     * Berikan poin kepada user (type: earn)
     */
    public function earn(
        User $user,
        int $amount,
        string $sourceType,
        int $sourceId,
        string $reference,
        string $description,
        ?User $creator = null
    ): PointTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Jumlah poin yang didapatkan harus lebih dari 0.');
        }

        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $reference, $description, $creator) {
            // Cek apakah transaksi dengan referensi ini sudah pernah dibuat (Idempotency)
            $existing = PointTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            // Kunci baris user untuk mencegah race condition
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = (int) $lockedUser->point_balance;
            $balanceAfter  = $balanceBefore + $amount;

            $lockedUser->point_balance = $balanceAfter;
            $lockedUser->save();

            $transaction = PointTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => 'earn',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'source_type'    => $sourceType,
                'source_id'      => $sourceId,
                'reference'      => $reference,
                'description'    => $description,
                'created_by'     => $creator?->id ?? $lockedUser->id,
            ]);

            $this->auditService->log(
                actor: $creator ?? $lockedUser,
                action: 'point_earn',
                target: $transaction,
                oldValues: ['balance' => $balanceBefore],
                newValues: ['balance' => $balanceAfter, 'earned' => $amount],
                description: "Penambahan poin +{$amount} untuk {$lockedUser->name} ({$description})"
            );

            return $transaction;
        });
    }

    /**
     * Kurangi poin user untuk penukaran reward (type: redeem)
     */
    public function redeem(
        User $user,
        int $amount,
        string $sourceType,
        int $sourceId,
        string $reference,
        string $description,
        ?User $creator = null
    ): PointTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Jumlah poin yang ditukarkan harus lebih dari 0.');
        }

        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $reference, $description, $creator) {
            // Cek apakah transaksi dengan referensi ini sudah pernah dibuat (Idempotency)
            $existing = PointTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            // Kunci baris user
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = (int) $lockedUser->point_balance;
            if ($balanceBefore < $amount) {
                throw new RuntimeException("Saldo poin tidak mencukupi (Saldo: {$balanceBefore}, Dibutuhkan: {$amount}).");
            }

            $balanceAfter = $balanceBefore - $amount;

            $lockedUser->point_balance = $balanceAfter;
            $lockedUser->save();

            $transaction = PointTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => 'redeem',
                'amount'         => -$amount, // Negatif pada buku besar
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'source_type'    => $sourceType,
                'source_id'      => $sourceId,
                'reference'      => $reference,
                'description'    => $description,
                'created_by'     => $creator?->id ?? $lockedUser->id,
            ]);

            $this->auditService->log(
                actor: $creator ?? $lockedUser,
                action: 'point_redeem',
                target: $transaction,
                oldValues: ['balance' => $balanceBefore],
                newValues: ['balance' => $balanceAfter, 'redeemed' => $amount],
                description: "Pengurangan poin -{$amount} untuk {$lockedUser->name} ({$description})"
            );

            return $transaction;
        });
    }

    /**
     * Kembalikan poin user akibat pembatalan atau penolakan penukaran (type: reversal)
     */
    public function reverse(
        User $user,
        int $amount,
        string $sourceType,
        int $sourceId,
        string $reference,
        string $description,
        ?User $actor = null
    ): PointTransaction {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Jumlah poin yang dikembalikan harus lebih dari 0.');
        }

        return DB::transaction(function () use ($user, $amount, $sourceType, $sourceId, $reference, $description, $actor) {
            // Pastikan reversal tidak diduplikasi
            $existing = PointTransaction::where('reference', $reference)->first();
            if ($existing) {
                return $existing;
            }

            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = (int) $lockedUser->point_balance;
            $balanceAfter  = $balanceBefore + $amount;

            $lockedUser->point_balance = $balanceAfter;
            $lockedUser->save();

            $transaction = PointTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => 'reversal',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'source_type'    => $sourceType,
                'source_id'      => $sourceId,
                'reference'      => $reference,
                'description'    => $description,
                'created_by'     => $actor?->id ?? $lockedUser->id,
            ]);

            $this->auditService->log(
                actor: $actor ?? $lockedUser,
                action: 'point_reverse',
                target: $transaction,
                oldValues: ['balance' => $balanceBefore],
                newValues: ['balance' => $balanceAfter, 'reversed' => $amount],
                description: "Pengembalian poin +{$amount} untuk {$lockedUser->name} ({$description})"
            );

            return $transaction;
        });
    }

    /**
     * Penyesuaian poin oleh Admin (type: adjustment)
     */
    public function adjust(
        User $user,
        int $amount,
        string $reason,
        User $admin
    ): PointTransaction {
        if ($amount === 0) {
            throw new InvalidArgumentException('Nilai adjustment tidak boleh 0.');
        }

        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan penyesuaian poin wajib diisi.');
        }

        return DB::transaction(function () use ($user, $amount, $reason, $admin) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();

            $balanceBefore = (int) $lockedUser->point_balance;
            $balanceAfter  = $balanceBefore + $amount;

            if ($balanceAfter < 0) {
                throw new RuntimeException("Penyesuaian tidak dapat dilakukan karena akan menghasilkan saldo negatif (Saldo saat ini: {$balanceBefore}, Perubahan: {$amount}).");
            }

            $lockedUser->point_balance = $balanceAfter;
            $lockedUser->save();

            $reference = 'ADJ-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

            $transaction = PointTransaction::create([
                'user_id'        => $lockedUser->id,
                'type'           => 'adjustment',
                'amount'         => $amount,
                'balance_before' => $balanceBefore,
                'balance_after'  => $balanceAfter,
                'source_type'    => 'manual_adjustment',
                'source_id'      => null,
                'reference'      => $reference,
                'description'    => "Penyesuaian oleh Admin ({$admin->name}): {$reason}",
                'created_by'     => $admin->id,
            ]);

            $this->auditService->log(
                actor: $admin,
                action: 'point_adjust',
                target: $transaction,
                oldValues: ['balance' => $balanceBefore],
                newValues: ['balance' => $balanceAfter, 'adjustment' => $amount],
                description: "Penyesuaian saldo poin ({$amount}) untuk {$lockedUser->name}. Alasan: {$reason}"
            );

            return $transaction;
        });
    }

    /**
     * Hitung ulang dan sinkronkan saldo user berdasarkan total ledger point_transactions
     */
    public function recalculateBalance(User $user): int
    {
        return DB::transaction(function () use ($user) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            $calculated = (int) PointTransaction::where('user_id', $lockedUser->id)->sum('amount');
            $balance    = max(0, $calculated);

            $lockedUser->point_balance = $balance;
            $lockedUser->save();

            return $balance;
        });
    }
}
