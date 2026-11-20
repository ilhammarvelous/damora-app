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
        Schema::create('overtime_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('attendance_day_id')->unique()->constrained('attendance_days')->restrictOnDelete();
            $table->foreignId('overtime_rule_id')->constrained('overtime_rules')->restrictOnDelete();
            $table->foreignId('overtime_start_log_id')->nullable()->unique()->constrained('attendance_logs')->restrictOnDelete();
            $table->timestampTz('scheduled_clock_out_at');
            $table->timestampTz('actual_clock_out_at');
            $table->integer('calculated_minutes');
            $table->integer('rounded_minutes');
            $table->integer('approved_minutes')->nullable();
            $table->decimal('rate_per_hour', 14, 2);
            $table->decimal('calculated_amount', 14, 2);
            $table->decimal('approved_amount', 14, 2)->nullable();
            $table->boolean('is_unconfirmed')->default(false);
            $table->string('status', 10)->default('menunggu');
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->foreignId('payroll_item_id')->nullable()->constrained('payroll_items')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['employee_id', 'status']);
            $table->index('company_id');
            $table->index('payroll_item_id');
            $table->index('overtime_rule_id');
        });

        DB::statement("ALTER TABLE overtime_records ADD CONSTRAINT overtime_records_status_chk CHECK (status IN ('menunggu', 'disetujui', 'ditolak'))");
        DB::statement("ALTER TABLE overtime_records ADD CONSTRAINT overtime_records_minutes_chk CHECK (calculated_minutes >= 0 AND rounded_minutes >= 0 AND (approved_minutes IS NULL OR approved_minutes >= 0))");
        DB::statement("ALTER TABLE overtime_records ADD CONSTRAINT overtime_records_amounts_chk CHECK (rate_per_hour >= 0 AND calculated_amount >= 0 AND (approved_amount IS NULL OR approved_amount >= 0))");
        DB::statement("ALTER TABLE overtime_records ADD CONSTRAINT overtime_records_span_chk CHECK (actual_clock_out_at > scheduled_clock_out_at)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_records');
    }
};
