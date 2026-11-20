<?php

namespace App\Models;

use App\Enums\AttendanceContext;
use App\Enums\AttendanceLogType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use LogicException;

// scope_key (generated) dan server_time (default now() di database) sengaja tidak fillable
#[Fillable([
    'idempotency_key', 'attendance_day_id', 'employee_id', 'log_type', 'context',
    'location_id', 'assignment_id', 'device_time', 'latitude', 'longitude', 'gps_accuracy_m',
    'distance_m', 'geofence_radius_m', 'within_geofence',
    'photo_file_id', 'exception_id', 'device_info', 'ip_address',
    'face_score', 'face_threshold', 'face_reference_file_id',
    'face_model_version', 'liveness_method', 'liveness_passed',
])]
class AttendanceLog extends Model
{
    public const UPDATED_AT = null;

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'log_type' => AttendanceLogType::class,
            'context' => AttendanceContext::class,
            'server_time' => 'datetime',
            'device_time' => 'datetime',
            'latitude' => 'decimal:6',
            'longitude' => 'decimal:6',
            'gps_accuracy_m' => 'decimal:2',
            'distance_m' => 'decimal:2',
            'geofence_radius_m' => 'integer',
            'within_geofence' => 'boolean',
            'face_score' => 'decimal:4',
            'face_threshold' => 'decimal:4',
            'liveness_passed' => 'boolean',
        ];
    }

    // Log absensi adalah bukti: tidak boleh diubah atau dihapus lewat model
    protected static function booted(): void
    {
        static::updating(function () {
            throw new LogicException('Log absensi tidak boleh diubah.');
        });

        static::deleting(function () {
            throw new LogicException('Log absensi tidak boleh dihapus.');
        });
    }

    // Skor lebih tinggi = lebih mirip. Balik arahnya bila model pengenalan wajah memakai jarak.
    public function faceMatched(): ?bool
    {
        if ($this->face_score === null || $this->face_threshold === null) {
            return null;
        }

        return (float) $this->face_score >= (float) $this->face_threshold;
    }

    public function attendanceDay(): BelongsTo { return $this->belongsTo(AttendanceDay::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
    public function location(): BelongsTo { return $this->belongsTo(Location::class); }
    public function assignment(): BelongsTo { return $this->belongsTo(Assignment::class); }
    public function photo(): BelongsTo { return $this->belongsTo(File::class, 'photo_file_id'); }
    public function faceReference(): BelongsTo { return $this->belongsTo(File::class, 'face_reference_file_id'); }
    public function exception(): BelongsTo { return $this->belongsTo(AttendanceException::class, 'exception_id'); }

    // Log mulai_lembur yang dipakai oleh satu catatan lembur
    public function overtimeRecord(): HasOne { return $this->hasOne(OvertimeRecord::class, 'overtime_start_log_id'); }
}
