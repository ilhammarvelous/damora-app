<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class CompanyOwnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $this->isActiveOwner($user);
    }

    public function view(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    public function create(User $user): bool
    {
        return $this->isActiveOwner($user);
    }

    public function update(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->ownsRecord($user, $model);
    }

    protected function isActiveOwner(User $user): bool
    {
        return $user->isOwner() && $user->isActive();
    }

    // Owner aktif, dan data milik perusahaannya sendiri
    protected function ownsRecord(User $user, Model $model): bool
    {
        return $this->isActiveOwner($user)
            && (int) $model->company_id === (int) $user->company_id;
    }
}
