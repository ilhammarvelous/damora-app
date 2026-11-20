<?php

namespace App\Models;

use App\Enums\AssignmentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'title', 'description', 'address', 'latitude', 'longitude',
    'radius_m', 'start_at', 'end_at', 'status', 'created_by',
])]
class Assignment extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => AssignmentStatus::class,
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'radius_m' => 'integer',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'assignment_employees', 'assignment_id', 'employee_id')
            ->using(AssignmentEmployee::class)
            ->withPivot('status', 'arrived_at', 'completed_at')
            ->withTimestamps();
    }

    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class); }
}
