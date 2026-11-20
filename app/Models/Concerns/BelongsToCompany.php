<?php

namespace App\Models\Concerns;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    protected static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope());

        // Pengaman terakhir: bila company_id belum diisi, ambil dari pengguna yang login
        static::creating(function (Model $model) {
            if ($model->company_id === null && Auth::hasUser()) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }
}
