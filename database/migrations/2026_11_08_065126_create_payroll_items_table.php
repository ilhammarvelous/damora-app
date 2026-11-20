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
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('pay_method', 10);
            $table->decimal('base_rate', 14, 2);
            $table->decimal('overtime_rate', 14, 2);
            $table->decimal('work_units', 10, 2);
            $table->decimal('base_amount', 14, 2);
            $table->integer('overtime_minutes')->default(0);
            $table->decimal('overtime_amount', 14, 2)->default(0);
            $table->decimal('adjustment_amount', 14, 2)->default(0);
            $table->decimal('deduction_amount', 14, 2)->default(0);
            $table->decimal('total_pay', 14, 2);
            $table->string('payment_status', 12)->default('belum_dibayar');
            $table->timestampTz('paid_at')->nullable();
            $table->foreignId('paid_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->unique(['payroll_period_id', 'employee_id']);
            $table->index('employee_id');
            $table->index('paid_by');
        });

        DB::statement("ALTER TABLE payroll_items ADD CONSTRAINT payroll_items_method_chk CHECK (pay_method IN ('per_jam', 'harian', 'mingguan', 'bulanan'))");
        DB::statement("ALTER TABLE payroll_items ADD CONSTRAINT payroll_items_pay_status_chk CHECK (payment_status IN ('belum_dibayar', 'diproses', 'sudah_dibayar'))");
        DB::statement("ALTER TABLE payroll_items ADD CONSTRAINT payroll_items_total_chk CHECK (total_pay = base_amount + overtime_amount + adjustment_amount - deduction_amount)");
        DB::statement("ALTER TABLE payroll_items ADD CONSTRAINT payroll_items_non_negative_chk CHECK (overtime_minutes >= 0 AND deduction_amount >= 0 AND base_amount >= 0 AND overtime_amount >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_items');
    }
};
