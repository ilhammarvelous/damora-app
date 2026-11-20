<?php

namespace App\Models;

use App\Enums\PayMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'payroll_period_id', 'employee_id', 'pay_method', 'base_rate', 'overtime_rate',
    'work_units', 'base_amount', 'overtime_minutes', 'overtime_amount',
    'adjustment_amount', 'deduction_amount', 'total_pay',
    'payment_status', 'paid_at', 'paid_by',
])]
class PayrollItem extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'pay_method' => PayMethod::class,
            'payment_status' => PaymentStatus::class,
            'base_rate' => 'decimal:2',
            'overtime_rate' => 'decimal:2',
            'work_units' => 'decimal:2',
            'base_amount' => 'decimal:2',
            'overtime_minutes' => 'integer',
            'overtime_amount' => 'decimal:2',
            'adjustment_amount' => 'decimal:2',
            'deduction_amount' => 'decimal:2',
            'total_pay' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function period(): BelongsTo { return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function payer(): BelongsTo { return $this->belongsTo(User::class, 'paid_by'); }
    public function lines(): HasMany { return $this->hasMany(PayrollItemLine::class); }
    public function payslip(): HasOne { return $this->hasOne(Payslip::class); }
    public function overtimeRecords(): HasMany { return $this->hasMany(OvertimeRecord::class); }
}
