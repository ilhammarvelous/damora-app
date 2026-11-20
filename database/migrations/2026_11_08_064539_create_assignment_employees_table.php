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
        Schema::create('assignment_employees', function (Blueprint $table) {
            $table->foreignId('assignment_id')->constrained('assignments')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('status', 12)->default('ditugaskan');
            $table->timestampTz('arrived_at')->nullable();
            $table->timestampTz('completed_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->primary(['assignment_id', 'employee_id']);
            $table->index(['employee_id', 'status']);
        });

        DB::statement("ALTER TABLE assignment_employees ADD CONSTRAINT assignment_employees_status_chk CHECK (status IN ('ditugaskan', 'tiba', 'selesai', 'terlewat'))");
        DB::statement("ALTER TABLE assignment_employees ADD CONSTRAINT assignment_employees_arrived_chk CHECK (status NOT IN ('tiba', 'selesai') OR arrived_at IS NOT NULL)");
        DB::statement("ALTER TABLE assignment_employees ADD CONSTRAINT assignment_employees_completed_chk CHECK (status <> 'selesai' OR completed_at IS NOT NULL)");
        DB::statement("ALTER TABLE assignment_employees ADD CONSTRAINT assignment_employees_time_chk CHECK (completed_at IS NULL OR arrived_at IS NULL OR completed_at >= arrived_at)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment_employees');
    }
};
