<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PayrollLineSourceType: string
{
    use HasOptions;

    case ATTENDANCE_DAYS = 'attendance_days';
    case OVERTIME_RECORDS = 'overtime_records';
    case LEAVE_REQUESTS = 'leave_requests';
    case MANUAL = 'manual';

    public function label(): string
    {
        return match ($this) {
            self::ATTENDANCE_DAYS => 'Absensi harian',
            self::OVERTIME_RECORDS => 'Lembur',
            self::LEAVE_REQUESTS => 'Pengajuan izin',
            self::MANUAL => 'Manual',
        };
    }
}
