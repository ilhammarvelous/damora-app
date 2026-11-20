<?php

namespace App\Models;

use App\Enums\AttendanceStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'employee_id', 'work_date', 'schedule_id',
    'scheduled_clock_in', 'scheduled_break_start', 'scheduled_break_end', 'scheduled_clock_out',
    'scheduled_start_at', 'scheduled_end_at', 'clock_in_at', 'clock_out_at',
    'work_minutes', 'late_minutes', 'status', 'leave_request_id',
])]

class AttendanceDay extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => AttendanceStatus::class,
            'work_date' => 'date',
            'scheduled_start_at' => 'datetime',
            'scheduled_end_at' => 'datetime',
            'clock_in_at' => 'datetime',
            'clock_out_at' => 'datetime',
            'work_minutes' => 'integer',
            'late_minutes' => 'integer',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function schedule(): BelongsTo { return $this->belongsTo(WorkSchedule::class, 'schedule_id'); }
    public function leaveRequest(): BelongsTo { return $this->belongsTo(LeaveRequest::class); }
    public function logs(): HasMany { return $this->hasMany(AttendanceLog::class); }
    public function overtimeRecord(): HasOne { return $this->hasOne(OvertimeRecord::class); }
}
