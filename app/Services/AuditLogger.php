<?php

namespace App\Services;

use App\Enums\AuditAction;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

final class AuditLogger
{
    // Kolom yang tidak boleh masuk catatan audit
    private const SENSITIVE = ['password_hash', 'face_embedding', 'storage_key', 'remember_token'];

    /**
     * @param  array<string, mixed>  $old
     * @param  array<string, mixed>  $new
     */
    public function record(
        User $actor,
        AuditAction $action,
        Model $entity,
        array $old = [],
        array $new = [],
        ?string $reason = null,
    ): AuditLog {
        return AuditLog::create([
            'company_id' => $entity->getAttribute('company_id') ?? $actor->company_id,
            'actor_user_id' => $actor->id,
            'entity_type' => class_basename($entity),
            'entity_id' => $entity->getKey(),
            'action' => $action,
            'old_value' => $old === [] ? null : Arr::except($old, self::SENSITIVE),
            'new_value' => $new === [] ? null : Arr::except($new, self::SENSITIVE),
            'reason' => $reason,
            'ip_address' => request()->ip(),
        ]);
    }
}
