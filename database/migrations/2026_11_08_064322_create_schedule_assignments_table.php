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
        Schema::create('schedule_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('work_schedules')->restrictOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->restrictOnDelete();
            $table->foreignId('group_id')->nullable()->constrained('employee_groups')->restrictOnDelete();
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['employee_id', 'effective_from']);
            $table->index(['group_id', 'effective_from']);
            $table->index('schedule_id');
        });

        DB::statement("ALTER TABLE schedule_assignments ADD CONSTRAINT schedule_assignments_target_chk CHECK (num_nonnulls(employee_id, group_id) = 1)");
        DB::statement("ALTER TABLE schedule_assignments ADD CONSTRAINT schedule_assignments_dates_chk CHECK (effective_until IS NULL OR effective_until >= effective_from)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_assignments');
    }
};
