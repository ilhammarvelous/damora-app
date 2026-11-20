<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PayrollLineType: string
{
    use HasOptions;

    case BASE = 'dasar';
    case OVERTIME = 'lembur';
    case ADJUSTMENT = 'koreksi';
    case DEDUCTION = 'potongan';
}
