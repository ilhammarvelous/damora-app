<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('purpose', 20);
            $table->string('storage_key', 255)->unique();
            $table->string('mime_type', 60);
            $table->integer('size_bytes');
            $table->char('sha256', 64);
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->date('retain_until')->nullable();
            $table->timestampTz('created_at')->useCurrent();

            $table->index('company_id');
            $table->index('sha256');
            $table->index('uploaded_by');
            $table->index(['purpose', 'retain_until']);
        });

        DB::statement("ALTER TABLE files ADD CONSTRAINT files_purpose_chk CHECK (purpose IN ('face_reference', 'attendance_photo', 'leave_attachment', 'payslip'))");
        DB::statement("ALTER TABLE files ADD CONSTRAINT files_mime_chk CHECK (mime_type IN ('image/jpeg', 'image/webp', 'application/pdf'))");
        DB::statement("ALTER TABLE files ADD CONSTRAINT files_size_chk CHECK (size_bytes > 0)");
        // foto referensi disimpan selama karyawan aktif, jadi tanggal hapusnya baru diisi saat karyawan berhenti
        DB::statement("ALTER TABLE files ADD CONSTRAINT files_retain_chk CHECK (purpose = 'face_reference' OR retain_until IS NOT NULL)");
        DB::statement("ALTER TABLE files ADD CONSTRAINT files_image_purpose_chk CHECK (purpose NOT IN ('face_reference', 'attendance_photo') OR mime_type IN ('image/jpeg', 'image/webp'))");
        DB::statement("ALTER TABLE files ADD CONSTRAINT files_payslip_mime_chk CHECK (purpose <> 'payslip' OR mime_type = 'application/pdf')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
