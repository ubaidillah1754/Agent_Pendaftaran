<?php

namespace App\Services;

use App\Models\Merchandise;
use App\Models\PointRedemption;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

class RedemptionService
{
    public function __construct(
        protected PointService $pointService,
        protected AuditService $auditService
    ) {}

    /**
     * Buat permohonan penukaran reward baru oleh karyawan.
     * Menggunakan Opsi A: Poin langsung dipotong dan stok di-reserve (status pending).
     */
    public function createRedemption(
        User $user,
        int $merchandiseId,
        int $quantity = 1,
        ?string $notes = null
    ): PointRedemption {
        if ($quantity <= 0) {
            throw new InvalidArgumentException('Jumlah item yang ditukarkan minimal 1.');
        }

        return DB::transaction(function () use ($user, $merchandiseId, $quantity, $notes) {
            // Lock merchandise untuk mencegah race condition stok habis
            $merchandise = Merchandise::where('id', $merchandiseId)->lockForUpdate()->first();
            if (!$merchandise || !$merchandise->is_active) {
                throw new RuntimeException('Merchandise tidak ditemukan atau sedang tidak aktif.');
            }

            if ($merchandise->stock < $quantity) {
                throw new RuntimeException("Stok tidak mencukupi (Sisa stok: {$merchandise->stock}, Diminta: {$quantity}).");
            }

            $totalPoints = (int) ($merchandise->points_required * $quantity);

            // Validasi saldo user
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->firstOrFail();
            if ($lockedUser->point_balance < $totalPoints) {
                throw new RuntimeException("Saldo poin Anda ({$lockedUser->point_balance}) tidak mencukupi untuk penukaran ini (Dibutuhkan: {$totalPoints} poin).");
            }

            // Kurangi stok merchandise
            $merchandise->stock -= $quantity;
            $merchandise->save();

            // Generate reference code unik
            $refCode = PointRedemption::generateReferenceCode();

            $redemption = PointRedemption::create([
                'reference_code'   => $refCode,
                'user_id'          => $lockedUser->id,
                'merchandise_id'   => $merchandise->id,
                'merchandise_name' => $merchandise->name,
                'points_required'  => $merchandise->points_required,
                'points'           => $totalPoints,
                'quantity'         => $quantity,
                'total_points'     => $totalPoints,
                'type'             => 'merchandise',
                'status'           => 'pending',
                'notes'            => $notes,
            ]);

            // Potong poin dan catat ke ledger
            $this->pointService->redeem(
                user: $lockedUser,
                amount: $totalPoints,
                sourceType: PointRedemption::class,
                sourceId: $redemption->id,
                reference: $refCode,
                description: "Penukaran reward: {$merchandise->name} ({$quantity}x) - Ref: {$refCode}",
                creator: $lockedUser
            );

            $this->auditService->log(
                actor: $lockedUser,
                action: 'redemption_created',
                target: $redemption,
                oldValues: ['stock' => $merchandise->stock + $quantity],
                newValues: ['stock' => $merchandise->stock, 'status' => 'pending', 'total_points' => $totalPoints],
                description: "Pengajuan penukaran {$merchandise->name} ({$quantity}x) oleh {$lockedUser->name} ({$totalPoints} poin)"
            );

            return $redemption;
        });
    }

    /**
     * Admin menyetujui penukaran (status: approved)
     */
    public function approve(PointRedemption $redemption, User $admin, ?string $notes = null): PointRedemption
    {
        return DB::transaction(function () use ($redemption, $admin, $notes) {
            $locked = PointRedemption::where('id', $redemption->id)->lockForUpdate()->firstOrFail();

            if (!$locked->isPending()) {
                throw new RuntimeException("Penukaran tidak dapat disetujui karena statusnya sudah {$locked->status_label}.");
            }

            $locked->status      = 'approved';
            $locked->approved_by = $admin->id;
            $locked->approved_at = now();
            if ($notes) {
                $locked->notes = $notes;
            }
            $locked->save();

            $this->auditService->log(
                actor: $admin,
                action: 'redemption_approved',
                target: $locked,
                oldValues: ['status' => 'pending'],
                newValues: ['status' => 'approved', 'approved_by' => $admin->name],
                description: "Penukaran {$locked->reference_code} ({$locked->merchandise_name}) disetujui oleh {$admin->name}"
            );

            return $locked;
        });
    }

    /**
     * Admin menandai penukaran telah selesai / barang diserahkan (status: completed)
     */
    public function complete(PointRedemption $redemption, User $admin, ?string $notes = null): PointRedemption
    {
        return DB::transaction(function () use ($redemption, $admin, $notes) {
            $locked = PointRedemption::where('id', $redemption->id)->lockForUpdate()->firstOrFail();

            if ($locked->isFinal()) {
                throw new RuntimeException("Penukaran sudah selesai atau tidak dapat diubah lagi.");
            }

            $oldStatus = $locked->status;
            $locked->status       = 'completed';
            $locked->completed_at = now();
            if ($notes) {
                $locked->notes = $notes;
            }
            $locked->save();

            $this->auditService->log(
                actor: $admin,
                action: 'redemption_completed',
                target: $locked,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'completed'],
                description: "Penukaran {$locked->reference_code} ({$locked->merchandise_name}) diselesaikan oleh {$admin->name}"
            );

            return $locked;
        });
    }

    /**
     * Admin menolak penukaran (status: rejected) dan mengembalikan stok + poin
     */
    public function reject(PointRedemption $redemption, User $admin, string $reason): PointRedemption
    {
        if (trim($reason) === '') {
            throw new InvalidArgumentException('Alasan penolakan penukaran wajib diisi.');
        }

        return DB::transaction(function () use ($redemption, $admin, $reason) {
            $locked = PointRedemption::where('id', $redemption->id)->lockForUpdate()->firstOrFail();

            if (!$locked->isPending()) {
                throw new RuntimeException("Penukaran tidak dapat ditolak karena statusnya sudah {$locked->status_label}.");
            }

            $locked->status      = 'rejected';
            $locked->rejected_at = now();
            $locked->notes       = $reason;
            $locked->save();

            // Kembalikan stok merchandise
            if ($locked->merchandise_id) {
                $merchandise = Merchandise::where('id', $locked->merchandise_id)->lockForUpdate()->first();
                if ($merchandise) {
                    $merchandise->stock += $locked->quantity;
                    $merchandise->save();
                }
            }

            // Kembalikan poin ke karyawan (type: reversal) jika poin lebih dari 0
            $user = User::where('id', $locked->user_id)->firstOrFail();
            if ($locked->total_points > 0) {
                $this->pointService->reverse(
                    user: $user,
                    amount: $locked->total_points,
                    sourceType: PointRedemption::class,
                    sourceId: $locked->id,
                    reference: "REV-{$locked->reference_code}",
                    description: "Pengembalian poin: Penukaran {$locked->merchandise_name} ditolak. Alasan: {$reason}",
                    actor: $admin
                );
            }

            $this->auditService->log(
                actor: $admin,
                action: 'redemption_rejected',
                target: $locked,
                oldValues: ['status' => 'pending'],
                newValues: ['status' => 'rejected', 'reason' => $reason],
                description: "Penukaran {$locked->reference_code} ditolak oleh {$admin->name}. Poin +{$locked->total_points} dikembalikan ke {$user->name}."
            );

            return $locked;
        });
    }

    /**
     * Pembatalan penukaran oleh Karyawan atau Admin (status: cancelled) dan mengembalikan stok + poin
     */
    public function cancel(PointRedemption $redemption, User $actor, string $reason): PointRedemption
    {
        return DB::transaction(function () use ($redemption, $actor, $reason) {
            $locked = PointRedemption::where('id', $redemption->id)->lockForUpdate()->firstOrFail();

            if ($locked->isFinal()) {
                throw new RuntimeException("Penukaran tidak dapat dibatalkan karena statusnya sudah {$locked->status_label}.");
            }

            // Hanya pemilik atau admin yang berhak membatalkan
            if (!$actor->isAdmin() && $actor->id !== $locked->user_id) {
                throw new RuntimeException("Anda tidak berhak membatalkan penukaran ini.");
            }

            $oldStatus = $locked->status;
            $locked->status       = 'cancelled';
            $locked->cancelled_at = now();
            $locked->notes        = $reason;
            $locked->save();

            // Kembalikan stok
            if ($locked->merchandise_id) {
                $merchandise = Merchandise::where('id', $locked->merchandise_id)->lockForUpdate()->first();
                if ($merchandise) {
                    $merchandise->stock += $locked->quantity;
                    $merchandise->save();
                }
            }

            // Kembalikan poin via reversal jika poin lebih dari 0
            $user = User::where('id', $locked->user_id)->firstOrFail();
            if ($locked->total_points > 0) {
                $this->pointService->reverse(
                    user: $user,
                    amount: $locked->total_points,
                    sourceType: PointRedemption::class,
                    sourceId: $locked->id,
                    reference: "REV-{$locked->reference_code}",
                    description: "Pengembalian poin: Penukaran {$locked->merchandise_name} dibatalkan. Alasan: {$reason}",
                    actor: $actor
                );
            }

            $this->auditService->log(
                actor: $actor,
                action: 'redemption_cancelled',
                target: $locked,
                oldValues: ['status' => $oldStatus],
                newValues: ['status' => 'cancelled', 'reason' => $reason],
                description: "Penukaran {$locked->reference_code} dibatalkan oleh {$actor->name}. Poin +{$locked->total_points} dikembalikan ke {$user->name}."
            );

            return $locked;
        });
    }
}
