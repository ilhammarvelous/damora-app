<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum OvertimeStatus: string
{
    use HasOptions;

    case PENDING = 'menunggu';
    case APPROVED = 'disetujui';
    case REJECTED = 'ditolak';
}
