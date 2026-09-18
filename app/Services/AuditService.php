<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditService
{
    public function record(?int $userId, string $action, object $entity, array $oldValues = [], array $newValues = [], ?Request $request = null): AuditLog
    {
        return AuditLog::create(['user_id' => $userId, 'action' => $action, 'entity_type' => $entity::class, 'entity_id' => $entity->getKey(), 'old_values' => $oldValues, 'new_values' => $newValues, 'ip_address' => $request?->ip(), 'created_at' => now()]);
    }
}