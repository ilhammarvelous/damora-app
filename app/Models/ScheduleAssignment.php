<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['schedule_id', 'employee_id', 'group_id', 'effective_from', 'effective_until'])]
class ScheduleAssignment extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }

    public function schedule(): BelongsTo { return $this->belongsTo(WorkSchedule::class, 'schedule_id'); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function group(): BelongsTo { return $this->belongsTo(EmployeeGroup::class, 'group_id'); }
}
