<?php

namespace App\Models;

use App\Enums\PayMethod;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'employee_id', 'pay_method', 'base_rate', 'overtime_rate_per_hour',
    'effective_from', 'effective_until', 'created_by',
])]
class EmployeePayRate extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'pay_method' => PayMethod::class,
            'base_rate' => 'decimal:2',
            'overtime_rate_per_hour' => 'decimal:2',
            'effective_from' => 'date',
            'effective_until' => 'date',
        ];
    }

    // Tarif yang berlaku pada tanggal tertentu
    public function scopeEffectiveOn($query, $date)
    {
        return $query->whereDate('effective_from', '<=', $date)
            ->where(fn ($w) => $w->whereNull('effective_until')->orWhereDate('effective_until', '>=', $date));
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function creator(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
