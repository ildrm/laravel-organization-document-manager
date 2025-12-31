<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLogService
{
    /**
     * Log an action
     */
    public function log(
        string $action,
        Model $model,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): AuditLog {
        $user = Auth::user();
        $organizationId = null;

        // Try to get organization from model or user
        if (method_exists($model, 'organization_id')) {
            $organizationId = $model->organization_id;
        } elseif ($user && $user->organization_id) {
            $organizationId = $user->organization_id;
        }

        return AuditLog::create([
            'user_id' => $user?->id,
            'organization_id' => $organizationId,
            'action' => $action,
            'model_type' => get_class($model),
            'model_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'description' => $description,
        ]);
    }

    /**
     * Log creation
     */
    public function logCreate(Model $model, ?string $description = null): AuditLog
    {
        return $this->log('created', $model, null, $model->getAttributes(), $description);
    }

    /**
     * Log update
     */
    public function logUpdate(Model $model, array $oldValues, ?string $description = null): AuditLog
    {
        return $this->log('updated', $model, $oldValues, $model->getAttributes(), $description);
    }

    /**
     * Log deletion
     */
    public function logDelete(Model $model, ?string $description = null): AuditLog
    {
        return $this->log('deleted', $model, $model->getAttributes(), null, $description);
    }

    /**
     * Log view
     */
    public function logView(Model $model, ?string $description = null): AuditLog
    {
        return $this->log('viewed', $model, null, null, $description);
    }
}
