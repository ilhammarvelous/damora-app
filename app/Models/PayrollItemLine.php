<?php

namespace App\Models;

use App\Enums\PayrollLineSourceType;
use App\Enums\PayrollLineType;
use App\Enums\PayrollLineUnit;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'payroll_item_id', 'line_type', 'description', 'quantity', 'unit',
    'rate', 'amount', 'source_type', 'source_id', 'reason', 'created_by',
])]
class PayrollItemLine extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'line_type' => PayrollLineType::class,
            'unit' => PayrollLineUnit::class,
            'source_type' => PayrollLineSourceType::class,
            'quantity' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
