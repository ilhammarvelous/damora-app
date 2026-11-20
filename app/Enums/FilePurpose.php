<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum FilePurpose: string
{
    use HasOptions;

    case FACE_REFERENCE = 'face_reference';
    case ATTENDANCE_PHOTO = 'attendance_photo';
    case LEAVE_ATTACHMENT = 'leave_attachment';
    case PAYSLIP = 'payslip';

    public function label(): string
    {
        return match ($this) {
            self::FACE_REFERENCE => 'Foto wajah referensi',
            self::ATTENDANCE_PHOTO => 'Foto absensi',
            self::LEAVE_ATTACHMENT => 'Lampiran izin',
            self::PAYSLIP => 'Slip gaji',
        };
    }

    /** @return list<FileMimeType> sama dengan aturan CHECK di tabel files */
    public function allowedMimeTypes(): array
    {
        return match ($this) {
            self::FACE_REFERENCE, self::ATTENDANCE_PHOTO => [FileMimeType::JPEG, FileMimeType::WEBP],
            self::LEAVE_ATTACHMENT => [FileMimeType::JPEG, FileMimeType::WEBP, FileMimeType::PDF],
            self::PAYSLIP => [FileMimeType::PDF],
        };
    }

    // Hanya foto referensi yang boleh tanpa tanggal hapus saat dibuat
    public function requiresRetainUntil(): bool
    {
        return $this !== self::FACE_REFERENCE;
    }
}
