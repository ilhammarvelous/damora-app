<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum EmployeeCategory: string
{
    use HasOptions;

    case OFFICE_STAFF = 'staf_kantor';
    case FIELD_SALES = 'sales_lapangan';
}
