<?php

namespace App\Models;

use App\Enums\DeductionAmountType;
use App\Enums\DeductionRuleType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'rule_type', 'is_active', 'interval_minutes',
    'tolerance_minutes', 'amount_type', 'amount', 'effective_from',
])]
class DeductionRule extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'rule_type' => DeductionRuleType::class,
            'amount_type' => DeductionAmountType::class,
            'is_active' => 'boolean',
            'interval_minutes' => 'integer',
            'tolerance_minutes' => 'integer',
            'amount' => 'decimal:2',
            'effective_from' => 'date',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
}
