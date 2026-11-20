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
        Schema::create('deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('rule_type', 14);
            $table->boolean('is_active')->default(false);
            $table->integer('interval_minutes')->nullable();
            $table->integer('tolerance_minutes')->default(0);
            $table->string('amount_type', 22);
            $table->decimal('amount', 14, 2);
            $table->date('effective_from');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['company_id', 'rule_type', 'effective_from']);
        });

        DB::statement("ALTER TABLE deduction_rules ADD CONSTRAINT deduction_rules_type_chk CHECK (rule_type IN ('terlambat', 'izin_berlebih', 'alpa'))");
        DB::statement("ALTER TABLE deduction_rules ADD CONSTRAINT deduction_rules_amount_type_chk CHECK (amount_type IN ('tetap', 'persen_upah_harian'))");
        DB::statement("ALTER TABLE deduction_rules ADD CONSTRAINT deduction_rules_amount_chk CHECK (amount >= 0 AND (amount_type <> 'persen_upah_harian' OR amount <= 100))");
        DB::statement("ALTER TABLE deduction_rules ADD CONSTRAINT deduction_rules_interval_chk CHECK ((interval_minutes IS NULL OR interval_minutes > 0) AND (rule_type <> 'terlambat' OR interval_minutes IS NOT NULL))");
        DB::statement("ALTER TABLE deduction_rules ADD CONSTRAINT deduction_rules_tolerance_chk CHECK (tolerance_minutes >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deduction_rules');
    }
};
