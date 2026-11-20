<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PayrollPeriodStatus: string
{
    use HasOptions;

    case DRAFT = 'draf';
    case FINAL = 'final';
}
