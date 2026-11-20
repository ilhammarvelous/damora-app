<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AttendanceContext: string
{
    use HasOptions;

    case OFFICE = 'kantor';
    case ASSIGNMENT = 'penugasan';
}
