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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('payment_date');
            $table->string('status', 10)->default('draf');
            $table->foreignId('finalized_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('finalized_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->unique(['company_id', 'start_date', 'end_date']);
            $table->index('finalized_by');
        });

        DB::statement("ALTER TABLE payroll_periods ADD CONSTRAINT payroll_periods_status_chk CHECK (status IN ('draf', 'final'))");
        DB::statement("ALTER TABLE payroll_periods ADD CONSTRAINT payroll_periods_dates_chk CHECK (end_date >= start_date)");
        DB::statement("ALTER TABLE payroll_periods ADD CONSTRAINT payroll_periods_finalized_chk CHECK (status = 'draf' OR (finalized_by IS NOT NULL AND finalized_at IS NOT NULL))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
