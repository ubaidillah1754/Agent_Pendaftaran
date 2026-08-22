<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Catat aktivitas sensitif ke tabel audit_logs.
     */
    public function log(
        User $actor,
        string $action,
        $target = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        $targetType = null;
        $targetId   = null;

        if ($target instanceof Model) {
            $targetType = get_class($target);
            $targetId   = $target->getKey();
        } elseif (is_string($target)) {
            $targetType = $target;
        }

        // Ambil IP dan UserAgent secara aman — request() bisa null di luar konteks HTTP
        $ipAddress  = '127.0.0.1';
        $userAgent  = null;
        try {
            if (app()->bound('request') && app('request') !== null) {
                $ipAddress = app('request')->ip() ?? '127.0.0.1';
                $userAgent = app('request')->userAgent();
            }
        } catch (\Throwable $e) {
            // Abaikan — konteks non-HTTP (queue, artisan, dll)
        }

        return AuditLog::create([
            'actor_id'    => $actor->id,
            'action'      => $action,
            'target_type' => $targetType,
            'target_id'   => $targetId,
            'old_values'  => $oldValues,
            'new_values'  => $newValues,
            'description' => $description,
            'ip_address'  => $ipAddress,
            'user_agent'  => $userAgent,
            'created_at'  => now(),
        ]);
    }
}
