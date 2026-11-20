<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Seeder ini hanya untuk pengembangan lokal
        if (app()->isProduction()) {
            throw new RuntimeException('UserSeeder tidak boleh dijalankan di produksi.');
        }

        $company = Company::where('name', 'PT Damora Jaya Abadi')->first();

        if ($company === null) {
            throw new RuntimeException('Perusahaan belum ada. Jalankan CompanySeeder terlebih dahulu.');
        }

        $email = 'owner@damora.test'; // ganti sesuai kebutuhan

        if (User::whereRaw('lower(email) = ?', [$email])->exists()) {
            $this->command?->warn("Owner {$email} sudah ada, dilewati.");
            return;
        }

        (new User())->forceFill([
            'company_id' => $company->id,
            'role' => UserRole::OWNER,
            'email' => $email,
            'password_hash' => 'DamoraDev2026', // hanya untuk lokal; di-hash otomatis oleh cast 'hashed'
            'status' => AccountStatus::ACTIVE,
            'must_change_password' => true,
        ])->save();

        $this->command?->info("Owner dibuat: {$email}");
    }
}
