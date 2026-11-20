<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum NotificationAction: string
{
    use HasOptions;

    case CLOCK_OUT = 'absen_pulang';
    case CONTINUE_OVERTIME = 'lanjut_lembur';
}
