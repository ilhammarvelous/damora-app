<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum CompanyStatus: string
{
    use HasOptions;

    case ACTIVE = 'aktif';
    case SUSPENDED = 'ditangguhkan';
}
