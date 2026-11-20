<?php

namespace App\Enums\Concerns;

trait HasOptions
{
    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->value));
    }

    /** @return string[] nilai yang tersimpan di database */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** @return array<int, array{value: string, label: string}> untuk dropdown/select */
    public static function options(): array
    {
        return array_map(
            fn (self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}
