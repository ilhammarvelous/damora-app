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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('title', 150);
            $table->text('description')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 9, 6);
            $table->decimal('longitude', 9, 6);
            $table->integer('radius_m');
            $table->timestampTz('start_at');
            $table->timestampTz('end_at');
            $table->string('status', 12)->default('draf');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['company_id', 'start_at', 'status']);
            $table->index('created_by');
        });

        DB::statement("ALTER TABLE assignments ADD CONSTRAINT assignments_status_chk CHECK (status IN ('draf', 'ditugaskan', 'berjalan', 'selesai', 'dibatalkan'))");
        DB::statement("ALTER TABLE assignments ADD CONSTRAINT assignments_span_chk CHECK (end_at > start_at)");
        DB::statement("ALTER TABLE assignments ADD CONSTRAINT assignments_radius_chk CHECK (radius_m > 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
