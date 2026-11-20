<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'name', 'shift_code', 'clock_in_time', 'break_start_time',
    'break_end_time', 'clock_out_time', 'crosses_midnight', 'late_tolerance_minutes',
    'clock_in_opens_minutes_before', 'is_default',
])]
class WorkSchedule extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'crosses_midnight' => 'boolean',
            'is_default' => 'boolean',
            'late_tolerance_minutes' => 'integer',
            'clock_in_opens_minutes_before' => 'integer',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function days(): HasMany { return $this->hasMany(ScheduleDay::class, 'schedule_id'); }
    public function assignments(): HasMany { return $this->hasMany(ScheduleAssignment::class, 'schedule_id'); }
    public function rosters(): HasMany { return $this->hasMany(ShiftRoster::class, 'schedule_id'); }
    public function attendanceDays(): HasMany { return $this->hasMany(AttendanceDay::class, 'schedule_id'); }
}
