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
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 60);
            $table->string('category', 10);
            $table->boolean('counts_toward_quota')->default(false);
            $table->decimal('monthly_quota_days', 4, 1)->nullable();
            $table->boolean('is_paid')->default(true);
            $table->boolean('requires_attachment')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index('company_id');
        });

        DB::statement("ALTER TABLE leave_types ADD CONSTRAINT leave_types_category_chk CHECK (category IN ('izin', 'sakit', 'cuti'))");
        DB::statement("ALTER TABLE leave_types ADD CONSTRAINT leave_types_quota_chk CHECK (monthly_quota_days IS NULL OR monthly_quota_days >= 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
