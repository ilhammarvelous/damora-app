<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class LocationPolicy extends CompanyOwnerPolicy
{
    // Lokasi tidak dihapus, hanya dinonaktifkan: riwayat absensi merujuk lokasi ini
    public function delete(User $user, Model $model): bool
    {
        return false;
    }
}
