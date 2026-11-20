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

         // dibutuhkan untuk exclusion constraint (rentang berlaku tidak boleh tumpang tindih)
        DB::statement('CREATE EXTENSION IF NOT EXISTS btree_gist');

        Schema::create('employee_pay_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->string('pay_method', 10);
            $table->decimal('base_rate', 14, 2);
            $table->decimal('overtime_rate_per_hour', 14, 2);
            $table->date('effective_from');
            $table->date('effective_until')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();

            $table->index(['employee_id', 'effective_from']);
            $table->index('created_by');
        });

        DB::statement("ALTER TABLE employee_pay_rates ADD CONSTRAINT employee_pay_rates_method_chk CHECK (pay_method IN ('per_jam', 'harian', 'mingguan', 'bulanan'))");
        DB::statement("ALTER TABLE employee_pay_rates ADD CONSTRAINT employee_pay_rates_amount_chk CHECK (base_rate >= 0 AND overtime_rate_per_hour >= 0)");
        DB::statement("ALTER TABLE employee_pay_rates ADD CONSTRAINT employee_pay_rates_dates_chk CHECK (effective_until IS NULL OR effective_until >= effective_from)");
        DB::statement("ALTER TABLE employee_pay_rates ADD CONSTRAINT employee_pay_rates_no_overlap EXCLUDE USING gist (employee_id WITH =, daterange(effective_from, effective_until, '[]') WITH &&)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_pay_rates');
    }
};
