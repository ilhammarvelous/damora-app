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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('name', 100);
            $table->string('shift_code', 20)->nullable();
            $table->time('clock_in_time');
            $table->time('break_start_time')->nullable();
            $table->time('break_end_time')->nullable();
            $table->time('clock_out_time');
            $table->boolean('crosses_midnight')->default(false);
            $table->integer('late_tolerance_minutes')->default(0);
            $table->integer('clock_in_opens_minutes_before')->default(60);
            $table->boolean('is_default')->default(false);
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index('company_id');
        });

        DB::statement("ALTER TABLE work_schedules ADD CONSTRAINT work_schedules_break_pair_chk CHECK ((break_start_time IS NULL) = (break_end_time IS NULL))");
        DB::statement("ALTER TABLE work_schedules ADD CONSTRAINT work_schedules_midnight_chk CHECK (crosses_midnight = (clock_out_time < clock_in_time))");
        DB::statement("ALTER TABLE work_schedules ADD CONSTRAINT work_schedules_in_out_chk CHECK (clock_in_time <> clock_out_time)");
        DB::statement("ALTER TABLE work_schedules ADD CONSTRAINT work_schedules_order_chk CHECK (crosses_midnight OR break_start_time IS NULL OR (clock_in_time < break_start_time AND break_start_time < break_end_time AND break_end_time < clock_out_time))");
        DB::statement("ALTER TABLE work_schedules ADD CONSTRAINT work_schedules_minutes_chk CHECK (late_tolerance_minutes >= 0 AND clock_in_opens_minutes_before >= 0)");

        DB::statement("CREATE UNIQUE INDEX work_schedules_one_default_idx ON work_schedules (company_id) WHERE is_default");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
