<?php

namespace App\Models;

use App\Enums\LeaveCategory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'name', 'category', 'counts_toward_quota',
    'monthly_quota_days', 'is_paid', 'requires_attachment', 'is_active',
])]
class LeaveType extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'category' => LeaveCategory::class,
            'counts_toward_quota' => 'boolean',
            'monthly_quota_days' => 'decimal:1',
            'is_paid' => 'boolean',
            'requires_attachment' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
}
