<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AttendanceStatus: string
{
    use HasOptions;

    case ON_TIME = 'tepat_waktu';
    case LATE = 'terlambat';
    case ABSENT = 'alpa';
    case INCOMPLETE = 'belum_lengkap';
    case HOLIDAY = 'libur';
    case LEAVE = 'izin';
}
