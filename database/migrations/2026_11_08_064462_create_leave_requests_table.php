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
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->restrictOnDelete();
            $table->foreignId('leave_type_id')->constrained('leave_types')->restrictOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('total_days', 4, 1);
            $table->text('reason');
            $table->foreignId('attachment_file_id')->nullable()->constrained('files')->restrictOnDelete();
            $table->string('status', 12)->default('menunggu');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestampTz('decided_at')->nullable();
            $table->text('decision_note')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['employee_id', 'start_date']);
            $table->index(['company_id', 'status']);
            $table->index('leave_type_id');
        });

        DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_status_chk CHECK (status IN ('menunggu', 'disetujui', 'ditolak', 'dibatalkan'))");
        DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_dates_chk CHECK (end_date >= start_date)");
        DB::statement("ALTER TABLE leave_requests ADD CONSTRAINT leave_requests_days_chk CHECK (total_days > 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
