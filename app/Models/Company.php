<?php

namespace App\Models;

use App\Enums\CompanyStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'timezone', 'initial_geofence_radius_m', 'status'])]
class Company extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'initial_geofence_radius_m' => 'integer',
            'status' => CompanyStatus::class,
        ];
    }

    public function settings(): HasMany { return $this->hasMany(CompanySetting::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
    public function employees(): HasMany { return $this->hasMany(Employee::class); }
    public function employeeGroups(): HasMany { return $this->hasMany(EmployeeGroup::class); }
    public function workSchedules(): HasMany { return $this->hasMany(WorkSchedule::class); }
    public function holidays(): HasMany { return $this->hasMany(Holiday::class); }
    public function locations(): HasMany { return $this->hasMany(Location::class); }
    public function files(): HasMany { return $this->hasMany(File::class); }
    public function attendanceDays(): HasMany { return $this->hasMany(AttendanceDay::class); }
    public function assignments(): HasMany { return $this->hasMany(Assignment::class); }
    public function leaveTypes(): HasMany { return $this->hasMany(LeaveType::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function overtimeRules(): HasMany { return $this->hasMany(OvertimeRule::class); }
    public function overtimeRecords(): HasMany { return $this->hasMany(OvertimeRecord::class); }
    public function deductionRules(): HasMany { return $this->hasMany(DeductionRule::class); }
    public function payrollPeriods(): HasMany { return $this->hasMany(PayrollPeriod::class); }
    public function auditLogs(): HasMany { return $this->hasMany(AuditLog::class); }
}
