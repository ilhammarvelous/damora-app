<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PayMethod: string
{
    use HasOptions;

    case HOURLY = 'per_jam';
    case DAILY = 'harian';
    case WEEKLY = 'mingguan';
    case MONTHLY = 'bulanan';
}
