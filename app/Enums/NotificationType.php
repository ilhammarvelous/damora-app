<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum NotificationType: string
{
    use HasOptions;

    case CLOCK_OUT_REMINDER = 'pengingat_pulang';
    case LEAVE_DECISION = 'keputusan_izin';
    case NEW_ASSIGNMENT = 'penugasan_baru';
}
