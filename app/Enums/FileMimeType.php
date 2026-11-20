<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum FileMimeType: string
{
    use HasOptions;

    case JPEG = 'image/jpeg';
    case WEBP = 'image/webp';
    case PDF = 'application/pdf';

    public function label(): string
    {
        return match ($this) {
            self::JPEG => 'Foto JPEG',
            self::WEBP => 'Foto WebP',
            self::PDF => 'Dokumen PDF',
        };
    }

    public function isImage(): bool
    {
        return $this !== self::PDF;
    }

    public function extension(): string
    {
        return match ($this) {
            self::JPEG => 'jpg',
            self::WEBP => 'webp',
            self::PDF => 'pdf',
        };
    }
}
