<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum UserRole: string
{
    use HasOptions;

    case OWNER = 'owner';
    case EMPLOYEE = 'karyawan';
}
