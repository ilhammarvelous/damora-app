<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_days', function (Blueprint $table) {
            $table->foreignId('schedule_id')->constrained('work_schedules')->restrictOnDelete();
            $table->smallInteger('day_of_week');
            $table->boolean('is_work_day')->default(true);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->primary(['schedule_id', 'day_of_week']);
        });

        DB::statement("ALTER TABLE schedule_days ADD CONSTRAINT schedule_days_dow_chk CHECK (day_of_week BETWEEN 1 AND 7)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_days');
    }
};
