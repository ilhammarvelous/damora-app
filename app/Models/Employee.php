<?php

namespace App\Models;

use App\Enums\AccountStatus;
use App\Enums\EmployeeCategory;
use App\Enums\FilePurpose;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\BelongsToCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use LogicException;

#[Fillable([
    'company_id', 'user_id', 'employee_code', 'full_name', 'phone', 'email',
    'job_title', 'category', 'start_date', 'end_date', 'status',
])]

#[Hidden(['face_embedding'])]

class Employee extends Model
{
    use BelongsToCompany;
    // Masa simpan foto referensi lama setelah diganti atau dicabut
    public const FACE_RETENTION_MONTHS = 12;

    // Sama dengan default di database, agar instans baru langsung punya status
    protected $attributes = [
        'status' => 'aktif',
    ];

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'category' => EmployeeCategory::class,
            'status' => AccountStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'face_embedding' => 'encrypted:array',
            'face_enrolled_at' => 'datetime',
            'face_consent_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AccountStatus::ACTIVE);
    }

    public function isFieldSales(): bool
    {
        return $this->category === EmployeeCategory::FIELD_SALES;
    }

    public function hasFaceReference(): bool
    {
        return $this->face_photo_file_id !== null;
    }

    // Boleh absen hanya bila aktif, belum berhenti, dan punya referensi wajah.
    public function canAttend(): bool
    {
        return $this->status === AccountStatus::ACTIVE
            && $this->hasFaceReference()
            && ($this->end_date === null || ! $this->end_date->isPast() || $this->end_date->isToday());
    }

    /**
     * Membuat karyawan baru sekaligus dengan referensi wajahnya dalam SATU insert
     * (database menolak karyawan aktif tanpa referensi: employees_face_required_chk).
     * Panggil di dalam transaksi yang sama dengan pembuatan `users`, `files`, dan audit_logs.
     *
     * @param  array<string, mixed>  $attributes
     * @param  float[]  $embedding
     */
    public static function createWithFace(
        array $attributes,
        File $photo,
        array $embedding,
        string $modelVersion,
        User $enrolledBy,
        CarbonInterface $consentAt,
        string $consentVersion,
    ): static {
        $employee = new static($attributes);
        $employee->assertValidFacePhoto($photo, $attributes['company_id'] ?? null);

        $employee->forceFill([
            'face_photo_file_id' => $photo->id,
            'face_embedding' => $embedding,
            'face_model_version' => $modelVersion,
            'face_enrolled_at' => now(),
            'face_enrolled_by' => $enrolledBy->id,
            'face_consent_at' => $consentAt,
            'face_consent_version' => $consentVersion,
        ])->save();

        return $employee;
    }

    /**
     * Mengganti foto referensi. Foto lama dijadwalkan dihapus sesuai masa retensi.
     *
     * @param  float[]  $embedding
     */
    public function enrollFace(
        File $photo,
        array $embedding,
        string $modelVersion,
        User $enrolledBy,
        CarbonInterface $consentAt,
        string $consentVersion,
    ): void {
        $this->assertValidFacePhoto($photo, $this->company_id);

        DB::transaction(function () use ($photo, $embedding, $modelVersion, $enrolledBy, $consentAt, $consentVersion) {
            $oldPhoto = $this->facePhoto;

            $this->forceFill([
                'face_photo_file_id' => $photo->id,
                'face_embedding' => $embedding,
                'face_model_version' => $modelVersion,
                'face_enrolled_at' => now(),
                'face_enrolled_by' => $enrolledBy->id,
                'face_consent_at' => $consentAt,
                'face_consent_version' => $consentVersion,
            ])->save();

            $oldPhoto?->scheduleRetention(now()->addMonths(self::FACE_RETENTION_MONTHS));
            $this->unsetRelation('facePhoto');
        });
    }

    /**
     * Mencabut referensi wajah (karyawan keluar atau menarik persetujuan).
     * Karyawan harus sudah dinonaktifkan. Template wajah (embedding) dihapus saat ini juga,
     * sedangkan foto dijadwalkan terhapus sesuai retensi lewat proses terjadwal `files`.
     */
    public function revokeFace(bool $withdrawConsent = false): void
    {
        if ($this->status === AccountStatus::ACTIVE) {
            throw new LogicException('Karyawan aktif wajib punya referensi wajah. Nonaktifkan karyawan terlebih dahulu.');
        }

        DB::transaction(function () use ($withdrawConsent) {
            $oldPhoto = $this->facePhoto;

            $attributes = [
                'face_photo_file_id' => null,
                'face_embedding' => null,
                'face_model_version' => null,
                'face_enrolled_at' => null,
                'face_enrolled_by' => null,
            ];

            if ($withdrawConsent) {
                $attributes['face_consent_at'] = null;
                $attributes['face_consent_version'] = null;
            }

            $this->forceFill($attributes)->save();

            $oldPhoto?->scheduleRetention(now()->addMonths(self::FACE_RETENTION_MONTHS));
            $this->unsetRelation('facePhoto');
        });
    }

    // Foto referensi harus berjenis face_reference dan milik perusahaan yang sama
    private function assertValidFacePhoto(File $photo, mixed $companyId): void
    {
        if ($photo->purpose !== FilePurpose::FACE_REFERENCE) {
            throw new LogicException('Berkas bukan foto wajah referensi.');
        }

        if ($companyId === null || (int) $photo->company_id !== (int) $companyId) {
            throw new LogicException('Foto referensi harus milik perusahaan yang sama dengan karyawan.');
        }
    }

    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function facePhoto(): BelongsTo { return $this->belongsTo(File::class, 'face_photo_file_id'); }
    public function faceEnroller(): BelongsTo { return $this->belongsTo(User::class, 'face_enrolled_by'); }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(EmployeeGroup::class, 'employee_group_members', 'employee_id', 'group_id')
            ->using(EmployeeGroupMember::class)
            ->withPivot('joined_on')
            ->withTimestamps();
    }

    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(Assignment::class, 'assignment_employees', 'employee_id', 'assignment_id')
            ->using(AssignmentEmployee::class)
            ->withPivot('status', 'arrived_at', 'completed_at')
            ->withTimestamps();
    }

    public function scheduleAssignments(): HasMany { return $this->hasMany(ScheduleAssignment::class); }
    public function shiftRosters(): HasMany { return $this->hasMany(ShiftRoster::class); }
    public function attendanceDays(): HasMany { return $this->hasMany(AttendanceDay::class); }
    public function attendanceLogs(): HasMany { return $this->hasMany(AttendanceLog::class); }
    public function attendanceExceptions(): HasMany { return $this->hasMany(AttendanceException::class); }
    public function leaveRequests(): HasMany { return $this->hasMany(LeaveRequest::class); }
    public function payRates(): HasMany { return $this->hasMany(EmployeePayRate::class); }
    public function overtimeRecords(): HasMany { return $this->hasMany(OvertimeRecord::class); }
    public function payrollItems(): HasMany { return $this->hasMany(PayrollItem::class); }
}
