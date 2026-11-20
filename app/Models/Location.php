<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'address', 'latitude', 'longitude', 'radius_m'])]

class Location extends Model
{
    use BelongsToCompany;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'radius_m' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query) { return $query->where('is_active', true); }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class); }
    public function attendanceExceptions(): HasMany { return $this->hasMany(AttendanceException::class); }
}
