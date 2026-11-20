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
        Schema::create('overtime_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('rounding_mode', 8);
            $table->integer('rounding_interval_minutes');
            $table->integer('minimum_minutes');
            $table->date('effective_from');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['company_id', 'effective_from']);
        });

        DB::statement("ALTER TABLE overtime_rules ADD CONSTRAINT overtime_rules_mode_chk CHECK (rounding_mode IN ('tidak', 'bawah', 'atas', 'terdekat'))");
        DB::statement("ALTER TABLE overtime_rules ADD CONSTRAINT overtime_rules_minutes_chk CHECK (rounding_interval_minutes > 0 AND minimum_minutes >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('overtime_rules');
    }
};
