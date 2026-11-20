<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AssignmentEmployeeStatus: string
{
    use HasOptions;

    case ASSIGNED = 'ditugaskan';
    case ARRIVED = 'tiba';
    case COMPLETED = 'selesai';
    case MISSED = 'terlewat';
}
