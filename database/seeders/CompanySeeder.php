<?php

namespace Database\Seeders;

use App\Enums\CompanyStatus;
use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['name' => 'PT Damora Jaya Abadi'], // ganti sesuai nama perusahaan Anda
            [
                'timezone' => 'Asia/Jakarta',
                'initial_geofence_radius_m' => 50,
                'status' => CompanyStatus::ACTIVE,
            ],
        );
    }
}
