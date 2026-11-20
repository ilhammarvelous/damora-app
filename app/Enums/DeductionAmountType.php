<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum DeductionAmountType: string
{
    use HasOptions;

    case FIXED = 'tetap';
    case PERCENT_OF_DAILY_WAGE = 'persen_upah_harian';
}
