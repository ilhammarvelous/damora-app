<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeGroupMember extends Pivot
{
    protected $table = 'employee_group_members';

    public $incrementing = false;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
        ];
    }
}
