<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DeductionRuleType: string
{
    use HasOptions;

    case LATE = 'terlambat';
    case EXCESS_LEAVE = 'izin_berlebih';
    case ABSENT = 'alpa';
}
