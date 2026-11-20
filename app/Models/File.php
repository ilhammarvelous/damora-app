<?php

namespace App\Models;

use App\Enums\FileMimeType;
use App\Enums\FilePurpose;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Concerns\BelongsToCompany;

#[Fillable([
    'company_id', 'purpose', 'storage_key', 'mime_type', 'size_bytes',
    'sha256', 'uploaded_by', 'retain_until',
])]

#[Hidden(['storage_key'])]

class File extends Model
{
    use BelongsToCompany;

    public const UPDATED_AT = null;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'purpose' => FilePurpose::class,
            'mime_type' => FileMimeType::class,
            'size_bytes' => 'integer',
            'retain_until' => 'date',
        ];
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function uploader(): BelongsTo { return $this->belongsTo(User::class, 'uploaded_by'); }

    // Karyawan yang foto referensinya adalah file ini (kosong bila sudah diganti atau dicabut)
    public function faceOwner(): HasOne { return $this->hasOne(Employee::class, 'face_photo_file_id'); }

    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class, 'photo_file_id'); }
    public function faceReferenceLogs(): HasMany { return $this->hasMany(AttendanceLog::class, 'face_reference_file_id'); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class, 'attachment_file_id'); }
    public function payslips(): HasMany { return $this->hasMany(Payslip::class, 'file_id'); }

    /**
     * Menentukan tanggal paling awal file boleh dihapus. Tidak pernah memperpendek tanggal yang sudah ada.
     */
    public function scheduleRetention(CarbonInterface $until): void
    {
        if ($this->retain_until === null || $this->retain_until->lt($until)) {
            $this->forceFill(['retain_until' => $until->toDateString()])->save();
        }
    }

    // File yang masa simpannya lewat dan tidak dirujuk tabel mana pun (aman dihapus)
    public function scopePurgeable(Builder $query): Builder
    {
        return $query
            ->whereNotNull('retain_until')
            ->where('retain_until', '<', now()->toDateString())
            ->whereDoesntHave('faceOwner')
            ->whereDoesntHave('attendanceLogs')
            ->whereDoesntHave('faceReferenceLogs')
            ->whereDoesntHave('leaveRequests')
            ->whereDoesntHave('payslips');
    }
}
