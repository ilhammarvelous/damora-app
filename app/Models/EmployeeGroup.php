<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\BelongsToCompany;

#[Fillable(['company_id', 'name'])]

class EmployeeGroup extends Model
{
    use BelongsToCompany;

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_group_members', 'group_id', 'employee_id')
            ->using(EmployeeGroupMember::class)
            ->withPivot('joined_on')
            ->withTimestamps();
    }

    public function scheduleAssignments(): HasMany { return $this->hasMany(ScheduleAssignment::class, 'group_id'); }
}
