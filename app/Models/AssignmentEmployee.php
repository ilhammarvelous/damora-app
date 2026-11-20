<?php

namespace App\Models;

use App\Enums\AssignmentEmployeeStatus;
use Illuminate\Database\Eloquent\Relations\Pivot;

class AssignmentEmployee extends Pivot
{
    protected $table = 'assignment_employees';

    public $incrementing = false;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => AssignmentEmployeeStatus::class,
            'arrived_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }
}
