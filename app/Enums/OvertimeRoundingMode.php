<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum OvertimeRoundingMode: string
{
    use HasOptions;

    case NONE = 'tidak';
    case DOWN = 'bawah';
    case UP = 'atas';
    case NEAREST = 'terdekat';

    public function label(): string
    {
        return match ($this) {
            self::NONE => 'Tanpa pembulatan',
            self::DOWN => 'Bulatkan ke bawah',
            self::UP => 'Bulatkan ke atas',
            self::NEAREST => 'Bulatkan ke terdekat',
        };
    }
}
