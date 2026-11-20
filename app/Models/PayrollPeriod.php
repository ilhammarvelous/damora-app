<?php

namespace App\Models;

use App\Enums\PayrollPeriodStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'name', 'start_date', 'end_date', 'payment_date',
    'status', 'finalized_by', 'finalized_at',
])]
class PayrollPeriod extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => PayrollPeriodStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'payment_date' => 'date',
            'finalized_at' => 'datetime',
        ];
    }

    public function isFinal(): bool { return $this->status === PayrollPeriodStatus::FINAL; }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function finalizer(): BelongsTo { return $this->belongsTo(User::class, 'finalized_by'); }
    public function items(): HasMany { return $this->hasMany(PayrollItem::class); }
}
