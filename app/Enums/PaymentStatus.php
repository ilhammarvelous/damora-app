<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum PaymentStatus: string
{
    use HasOptions;

    case UNPAID = 'belum_dibayar';
    case PROCESSING = 'diproses';
    case PAID = 'sudah_dibayar';
}
