<?php

namespace App\Models;

use App\Enums\AttendanceExceptionStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'employee_id', 'work_date', 'location_id', 'reason', 'status',
    'decided_by', 'decided_at', 'decision_note', 'expires_at',
])]
class AttendanceException extends Model
{
    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'status' => AttendanceExceptionStatus::class,
            'work_date' => 'date',
            'decided_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    // Sesuaikan nama case (APPROVED, USED, dan seterusnya) dengan enum Anda
    public function scopeUsable(Builder $query): Builder
    {
        return $query
            ->where('status', AttendanceExceptionStatus::APPROVED)
            ->where(fn (Builder $q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
    }

    public function isUsable(): bool
    {
        return $this->status === AttendanceExceptionStatus::APPROVED
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    // Dipanggil dalam transaksi yang sama dengan pembuatan log absensi
    public function markUsed(): void
    {
        $this->forceFill(['status' => AttendanceExceptionStatus::USED])->save();
    }

    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function decider(): BelongsTo { return $this->belongsTo(User::class, 'decided_by'); }
    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class, 'exception_id'); }
}
