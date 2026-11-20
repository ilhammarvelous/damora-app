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
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->uuid('idempotency_key')->unique(); // mencegah absen ganda saat sinyal lambat
            $table->foreignId('attendance_day_id')->constrained('attendance_days')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('log_type', 16);
            $table->string('context', 10);
            $table->foreignId('location_id')->nullable()->constrained('locations')->restrictOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('assignments')->restrictOnDelete();
            $table->timestampTz('server_time', 6)->useCurrent();
            $table->timestampTz('device_time', 6)->nullable(); // hanya pembanding, bukan waktu resmi
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->decimal('gps_accuracy_m', 7, 2)->nullable();
            $table->decimal('distance_m', 9, 2);
            $table->integer('geofence_radius_m');
            $table->boolean('within_geofence');
            $table->foreignId('photo_file_id')->nullable()->constrained('files')->restrictOnDelete();
            $table->foreignId('exception_id')->nullable()->constrained('attendance_exceptions')->restrictOnDelete();
            $table->string('device_info', 255)->nullable();
            $table->ipAddress('ip_address')->nullable();

            // Hasil verifikasi wajah (wajib untuk masuk dan pulang)
            $table->decimal('face_score', 6, 4)->nullable();
            $table->decimal('face_threshold', 6, 4)->nullable();
            $table->foreignId('face_reference_file_id')->nullable()->constrained('files')->restrictOnDelete();
            $table->string('face_model_version', 40)->nullable();
            $table->string('liveness_method', 20)->nullable();
            $table->boolean('liveness_passed')->nullable();

            $table->timestampTz('created_at')->useCurrent();

            // absensi tugas hanya sah untuk karyawan yang ditugaskan
            $table->foreign(['assignment_id', 'employee_id'])
                ->references(['assignment_id', 'employee_id'])->on('assignment_employees')
                ->restrictOnDelete();
            $table->index('face_reference_file_id');
            $table->index('photo_file_id');
            $table->index('exception_id');
            $table->index(['employee_id', 'server_time']);
            $table->index('location_id');
            $table->index('assignment_id');
        });

        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_type_chk CHECK (log_type IN ('masuk', 'mulai_istirahat', 'selesai_istirahat', 'pulang', 'mulai_lembur'))");
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_context_chk CHECK (context IN ('kantor', 'penugasan'))");
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_scope_ref_chk CHECK ((context = 'kantor' AND location_id IS NOT NULL AND assignment_id IS NULL) OR (context = 'penugasan' AND assignment_id IS NOT NULL AND location_id IS NULL))");
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_photo_chk CHECK (log_type = 'mulai_lembur' OR photo_file_id IS NOT NULL)");
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_geo_chk CHECK (distance_m >= 0 AND geofence_radius_m > 0)");

        // wajah wajib diverifikasi hanya untuk masuk dan pulang
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_face_required_chk CHECK (
            log_type NOT IN ('masuk', 'pulang')
            OR (face_score IS NOT NULL AND face_threshold IS NOT NULL AND face_reference_file_id IS NOT NULL AND face_model_version IS NOT NULL)
        )");
        // skor harus mencapai ambang (skor lebih tinggi = lebih mirip; balik arah bila model memakai jarak)
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_face_score_chk CHECK (face_score IS NULL OR face_threshold IS NULL OR face_score >= face_threshold)");
        // metode liveness dan hasilnya terisi berpasangan
        DB::statement("ALTER TABLE attendance_logs ADD CONSTRAINT attendance_logs_liveness_chk CHECK ((liveness_method IS NULL) = (liveness_passed IS NULL))");

        // kolom turunan: KANTOR atau P + assignment_id (tidak bisa tidak sinkron)
        DB::statement("ALTER TABLE attendance_logs ADD COLUMN scope_key VARCHAR(30) GENERATED ALWAYS AS (CASE WHEN context = 'kantor' THEN 'KANTOR' ELSE 'P' || assignment_id::text END) STORED NOT NULL");
        // mencegah absensi ganda
        DB::statement('CREATE UNIQUE INDEX attendance_logs_no_duplicate_idx ON attendance_logs (attendance_day_id, log_type, scope_key)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
