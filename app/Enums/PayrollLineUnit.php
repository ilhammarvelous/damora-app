<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PayrollLineUnit: string
{
    use HasOptions;

    case HOUR = 'jam';
    case DAY = 'hari';
    case WEEK = 'minggu';
    case MONTH = 'bulan';
}
