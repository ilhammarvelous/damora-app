<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        // Pengguna sudah dikenali: hanya data perusahaannya
        if (Auth::hasUser()) {
            $builder->where($model->qualifyColumn('company_id'), Auth::user()->company_id);

            return;
        }

        // Tanpa pengguna: hanya konsol (seeder, perintah artisan) yang boleh melihat semuanya.
        // Permintaan web tanpa login mendapat hasil kosong, bukan data semua perusahaan.
        if (! app()->runningInConsole()) {
            $builder->whereRaw('1 = 0');
        }
    }
}
