<?php

namespace App\Enums;

use App\Enums\Concerns\HasOptions;

enum AuditAction: string
{
    use HasOptions;

    case CREATE = 'buat';
    case UPDATE = 'ubah';
    case APPROVE = 'setujui';
    case REJECT = 'tolak';
    case FINALIZE = 'finalisasi';
    case DEACTIVATE = 'nonaktifkan';
    case ACTIVATE = 'aktifkan';
    case ATTENDANCE_REJECTED = 'absensi_ditolak';
    case FACE_ENROLLED = 'daftar_wajah';
    case FACE_REPLACED = 'ganti_wajah';
    case FACE_REVOKED = 'cabut_wajah';
    case FACE_MISMATCH = 'wajah_tidak_cocok';
}
