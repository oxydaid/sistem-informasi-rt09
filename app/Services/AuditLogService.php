<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogService
{
    /**
     * Log a user activity.
     */
    public function log(
        string $action,
        ?string $modelType = null,
        ?int $modelId = null,
        mixed $oldValues = null,
        mixed $newValues = null,
        ?int $userId = null
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $userId ?? Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues ? (is_array($oldValues) ? $oldValues : json_decode(json_encode($oldValues), true)) : null,
            'new_values' => $newValues ? (is_array($newValues) ? $newValues : json_decode(json_encode($newValues), true)) : null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }
}
