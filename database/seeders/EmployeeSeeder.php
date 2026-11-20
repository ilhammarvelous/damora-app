<?php

namespace Database\Seeders;

use App\Enums\AccountStatus;
use App\Enums\FileMimeType;
use App\Enums\FilePurpose;
use App\Enums\UserRole;
use App\Models\Company;
use App\Models\Employee;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class EmployeeSeeder extends Seeder
{
    private const COMPANY_NAME = 'PT Damora Jaya Abadi';
    private const OWNER_EMAIL = 'owner@damora.test';
    private const DUMMY_PASSWORD = 'KaryawanDev2026'; // hanya untuk lokal

    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('EmployeeSeeder tidak boleh dijalankan di produksi.');
        }

        $company = Company::where('name', self::COMPANY_NAME)->first();
        $owner = User::whereRaw('lower(email) = ?', [self::OWNER_EMAIL])->first();

        if ($company === null || $owner === null) {
            throw new RuntimeException('Jalankan CompanySeeder dan UserSeeder terlebih dahulu.');
        }

        $karyawan = [
            ['code' => 'K001', 'name' => 'Budi Santoso', 'email' => 'budi@damora.test',
             'phone' => '081200000001', 'job' => 'Staf Administrasi', 'category' => 'staf_kantor'],
            ['code' => 'K002', 'name' => 'Siti Rahma', 'email' => 'siti@damora.test',
             'phone' => '081200000002', 'job' => 'Staf Keuangan', 'category' => 'staf_kantor'],
            ['code' => 'K003', 'name' => 'Andi Pratama', 'email' => 'andi@damora.test',
             'phone' => '081200000003', 'job' => 'Sales Lapangan', 'category' => 'sales_lapangan'],
        ];

        foreach ($karyawan as $i => $row) {
            if (User::whereRaw('lower(email) = ?', [$row['email']])->exists()) {
                $this->command?->warn("{$row['email']} sudah ada, dilewati.");
                continue;
            }

            DB::transaction(function () use ($company, $owner, $row, $i) {
                // 1. Catatan berkas foto (tanpa gambar fisik, hanya data uji)
                $file = new File();
                $file->forceFill([
                    'company_id' => $company->id,
                    'purpose' => FilePurpose::FACE_REFERENCE->value,
                    'storage_key' => 'dummy/face/'.strtolower($row['code']).'.jpg',
                    'mime_type' => FileMimeType::JPEG->value,
                    'size_bytes' => 1024,
                    'sha256' => hash('sha256', 'dummy-face-'.$row['code']),
                    'uploaded_by' => $owner->id,
                    'retain_until' => null, // foto referensi boleh tanpa tanggal hapus
                ])->save();

                // 2. Akun login karyawan
                $user = new User();
                $user->forceFill([
                    'company_id' => $company->id,
                    'role' => UserRole::EMPLOYEE->value,
                    'email' => $row['email'],
                    'password_hash' => self::DUMMY_PASSWORD, // di-hash oleh cast 'hashed'
                    'status' => AccountStatus::ACTIVE->value,
                    'must_change_password' => true,
                ])->save();

                // 3. Data karyawan beserta seluruh kolom wajah sekaligus
                $employee = new Employee();
                $employee->forceFill([
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'employee_code' => $row['code'],
                    'full_name' => $row['name'],
                    'phone' => $row['phone'],
                    'email' => $row['email'],
                    'job_title' => $row['job'],
                    'category' => $row['category'],
                    'start_date' => '2026-01-01',
                    'status' => AccountStatus::ACTIVE->value,
                    'face_photo_file_id' => $file->id,
                    'face_embedding' => $this->dummyEmbedding($i + 1),
                    'face_model_version' => 'dummy-1',
                    'face_enrolled_at' => now(),
                    'face_enrolled_by' => $owner->id,
                    // face_consent_at dan face_consent_version dibiarkan kosong (nullable)
                ])->save();
            });

            $this->command?->info("Karyawan dibuat: {$row['code']} {$row['name']}");
        }
    }

    // Embedding palsu 128 angka, berbeda untuk tiap karyawan. Hanya untuk pengujian.
    private function dummyEmbedding(int $seed): array
    {
        return array_map(
            fn (int $n) => round(sin($seed * $n), 6),
            range(1, 128),
        );
    }
}
