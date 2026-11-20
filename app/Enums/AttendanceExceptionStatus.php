<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AttendanceExceptionStatus: string
{
    use HasOptions;

    case PENDING = 'menunggu';
    case APPROVED = 'disetujui';
    case REJECTED = 'ditolak';
    case USED = 'terpakai';
    case EXPIRED = 'kadaluwarsa';
}
