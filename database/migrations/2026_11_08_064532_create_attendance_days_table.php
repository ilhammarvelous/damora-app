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
        Schema::create('attendance_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->date('work_date');
            $table->foreignId('schedule_id')->nullable()->constrained('work_schedules')->restrictOnDelete();
            $table->time('scheduled_clock_in')->nullable();
            $table->time('scheduled_break_start')->nullable();
            $table->time('scheduled_break_end')->nullable();
            $table->time('scheduled_clock_out')->nullable();
            $table->timestampTz('scheduled_start_at')->nullable();
            $table->timestampTz('scheduled_end_at')->nullable();
            $table->timestampTz('clock_in_at')->nullable();
            $table->timestampTz('clock_out_at')->nullable();
            $table->integer('work_minutes')->nullable();
            $table->integer('late_minutes')->default(0);
            $table->string('status', 14);
            $table->foreignId('leave_request_id')->nullable()->constrained('leave_requests')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->unique(['employee_id', 'work_date']);
            $table->index(['company_id', 'work_date', 'status']);
            $table->index('schedule_id');
            $table->index('leave_request_id');
        });

        DB::statement("ALTER TABLE attendance_days ADD CONSTRAINT attendance_days_status_chk CHECK (status IN ('tepat_waktu', 'terlambat', 'alpa', 'belum_lengkap', 'libur', 'izin'))");
        DB::statement("ALTER TABLE attendance_days ADD CONSTRAINT attendance_days_minutes_chk CHECK (late_minutes >= 0 AND (work_minutes IS NULL OR work_minutes >= 0))");
        DB::statement("ALTER TABLE attendance_days ADD CONSTRAINT attendance_days_sched_span_chk CHECK (scheduled_end_at IS NULL OR scheduled_start_at IS NULL OR scheduled_end_at > scheduled_start_at)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_days');
    }
};
