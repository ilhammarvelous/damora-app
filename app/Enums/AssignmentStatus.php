<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AssignmentStatus: string
{
    use HasOptions;

    case DRAFT = 'draf';
    case ASSIGNED = 'ditugaskan';
    case IN_PROGRESS = 'berjalan';
    case COMPLETED = 'selesai';
    case CANCELLED = 'dibatalkan';
}
