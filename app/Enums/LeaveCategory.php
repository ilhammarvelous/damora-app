<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum LeaveCategory: string
{
    use HasOptions;

    case PERMISSION = 'izin';
    case SICK = 'sakit';
    case ANNUAL = 'cuti';
}
