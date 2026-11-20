<?php

namespace App\Models;

use App\Models\Concerns\HasCompositeKey;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['schedule_id', 'day_of_week', 'is_work_day'])]
class ScheduleDay extends Model
{
    use HasCompositeKey;

    protected $primaryKey = ['schedule_id', 'day_of_week'];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'day_of_week' => 'integer',
            'is_work_day' => 'boolean',
        ];
    }

    public function schedule(): BelongsTo { return $this->belongsTo(WorkSchedule::class, 'schedule_id'); }
}
