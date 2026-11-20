<?php

namespace App\Models;

use App\Enums\LeaveRequestStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'employee_id', 'leave_type_id', 'start_date', 'end_date',
    'total_days', 'reason', 'attachment_file_id', 'status',
    'requested_by', 'decided_by', 'decided_at', 'decision_note',
])]
class LeaveRequest extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => LeaveRequestStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'total_days' => 'decimal:1',
            'decided_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function leaveType(): BelongsTo { return $this->belongsTo(LeaveType::class); }
    public function attachment(): BelongsTo { return $this->belongsTo(File::class, 'attachment_file_id'); }
    public function requester(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function decider(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
    public function attendanceDays(): HasMany { return $this->hasMany(AttendanceDay::class); }
}
