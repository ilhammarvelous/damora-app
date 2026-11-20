<?php

namespace App\Models;

use App\Enums\AuditAction;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'actor_user_id', 'entity_type', 'entity_id',
    'action', 'old_value', 'new_value', 'reason', 'ip_address',
])]

class AuditLog extends Model
{
    use BelongsToCompany;

    public const UPDATED_AT = null;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'action' => AuditAction::class,
            'old_value' => 'array',
            'new_value' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::updating(fn () => throw new \LogicException('Catatan audit tidak boleh diubah.'));
        static::deleting(fn () => throw new \LogicException('Catatan audit tidak boleh dihapus.'));
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_user_id');
    }
}
