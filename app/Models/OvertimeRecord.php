<?php

namespace App\Models;

use App\Enums\OvertimeStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'employee_id', 'attendance_day_id', 'overtime_rule_id',
    'overtime_start_log_id', 'scheduled_clock_out_at', 'actual_clock_out_at',
    'calculated_minutes', 'rounded_minutes', 'approved_minutes',
    'rate_per_hour', 'calculated_amount', 'approved_amount',
    'is_unconfirmed', 'status', 'decided_by', 'decided_at', 'decision_note',
    'payroll_item_id',
])]
class OvertimeRecord extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => OvertimeStatus::class,
            'scheduled_clock_out_at' => 'datetime',
            'actual_clock_out_at' => 'datetime',
            'calculated_minutes' => 'integer',
            'rounded_minutes' => 'integer',
            'approved_minutes' => 'integer',
            'rate_per_hour' => 'decimal:2',
            'calculated_amount' => 'decimal:2',
            'approved_amount' => 'decimal:2',
            'is_unconfirmed' => 'boolean',
            'decided_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function attendanceDay(): BelongsTo { return $this->belongsTo(AttendanceDay::class); }
    public function overtimeRule(): BelongsTo { return $this->belongsTo(OvertimeRule::class); }
    public function startLog(): BelongsTo { return $this->belongsTo(AttendanceLog::class, 'overtime_start_log_id'); }
    public function decider(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
    public function payrollItem(): BelongsTo { return $this->belongsTo(PayrollItem::class); }
}
