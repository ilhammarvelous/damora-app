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
        Schema::create('attendance_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->date('work_date');
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->text('reason');
            $table->string('status', 12)->default('menunggu');
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestampTz('expires_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['employee_id', 'work_date']);
            $table->index('location_id');
            $table->index(['status', 'expires_at']);
            $table->index('decided_by');
        });

        DB::statement("ALTER TABLE attendance_exceptions ADD CONSTRAINT attendance_exceptions_status_chk CHECK (status IN ('menunggu', 'disetujui', 'ditolak', 'terpakai', 'kadaluwarsa'))");
        DB::statement("ALTER TABLE attendance_exceptions ADD CONSTRAINT attendance_exceptions_decision_chk CHECK (status IN ('menunggu', 'kadaluwarsa') OR (decided_by IS NOT NULL AND decided_at IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_exceptions');
    }
};
