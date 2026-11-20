<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AccountStatus: string
{
    use HasOptions;

    case ACTIVE = 'aktif';
    case INACTIVE = 'nonaktif';
    case ARCHIVED = 'diarsipkan';
}
