<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete();
            $table->string('employee_code', 30);
            $table->string('full_name', 150);
            $table->string('phone', 25)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('job_title', 100)->nullable();
            $table->string('category', 14);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('status', 12)->default('aktif');

            $table->foreignId('face_photo_file_id')->nullable()->unique()->constrained('files')->restrictOnDelete();
            $table->text('face_embedding')->nullable(); // simpan terenkripsi (encrypted cast)
            $table->string('face_model_version', 40)->nullable();
            $table->timestampTz('face_enrolled_at')->nullable();
            $table->foreignId('face_enrolled_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('face_consent_at')->nullable();
            $table->string('face_consent_version', 20)->nullable();

            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->unique(['company_id', 'employee_code']);
            $table->index(['company_id', 'status']);
            $table->index('face_enrolled_by');
        });

        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_category_chk CHECK (category IN ('staf_kantor', 'sales_lapangan'))");
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_status_chk CHECK (status IN ('aktif', 'nonaktif', 'diarsipkan'))");
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_dates_chk CHECK (end_date IS NULL OR end_date >= start_date)");
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_face_required_chk CHECK (status <> 'aktif' OR face_photo_file_id IS NOT NULL)");
        // kolom wajah (foto, embedding, model, waktu dan petugas pendaftaran) terisi atau kosong bersamaan
        DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_face_pair_chk CHECK (
            (face_photo_file_id IS NULL) = (face_embedding IS NULL)
            AND (face_photo_file_id IS NULL) = (face_model_version IS NULL)
            AND (face_photo_file_id IS NULL) = (face_enrolled_at IS NULL)
            AND (face_photo_file_id IS NULL) = (face_enrolled_by IS NULL)
        )");

        // persetujuan: waktu dan versinya berpasangan, dan wajib ada selama foto ada.
        // Setelah foto dicabut, catatan persetujuan boleh tetap tersimpan sebagai riwayat.
        // DB::statement("ALTER TABLE employees ADD CONSTRAINT employees_face_consent_chk CHECK (
        //     (face_consent_at IS NULL) = (face_consent_version IS NULL)
        //     AND (face_photo_file_id IS NULL OR face_consent_at IS NOT NULL)
        // )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');

    }
};
