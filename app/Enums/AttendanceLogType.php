<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AttendanceLogType: string
{
    use HasOptions;

    case CLOCK_IN = 'masuk';
    case BREAK_START = 'mulai_istirahat';
    case BREAK_END = 'selesai_istirahat';
    case CLOCK_OUT = 'pulang';
    case OVERTIME_START = 'mulai_lembur';

    // public function requiresFaceVerification(): bool
    // {
    //     return in_array($this->value, ['masuk', 'pulang'], true);
    // }
}
