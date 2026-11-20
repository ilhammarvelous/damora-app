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
        Schema::create('payroll_item_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_item_id')->constrained('payroll_items')->restrictOnDelete();
            $table->string('line_type', 10);
            $table->string('description', 255);
            $table->decimal('quantity', 10, 2);
            $table->string('unit', 10);
            $table->decimal('rate', 14, 2);
            $table->decimal('amount', 14, 2);
            $table->string('source_type', 30)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index('payroll_item_id');
            $table->index(['source_type', 'source_id']);
            $table->index('created_by');
        });

        DB::statement("ALTER TABLE payroll_item_lines ADD CONSTRAINT payroll_item_lines_type_chk CHECK (line_type IN ('dasar', 'lembur', 'koreksi', 'potongan'))");
        DB::statement("ALTER TABLE payroll_item_lines ADD CONSTRAINT payroll_item_lines_unit_chk CHECK (unit IN ('jam', 'hari', 'minggu', 'bulan'))");
        DB::statement("ALTER TABLE payroll_item_lines ADD CONSTRAINT payroll_item_lines_source_chk CHECK (source_type IS NULL OR source_type IN ('attendance_days', 'overtime_records', 'leave_requests', 'manual'))");
        DB::statement("ALTER TABLE payroll_item_lines ADD CONSTRAINT payroll_item_lines_reason_chk CHECK (line_type <> 'koreksi' OR (reason IS NOT NULL AND btrim(reason) <> ''))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_item_lines');
    }
};
