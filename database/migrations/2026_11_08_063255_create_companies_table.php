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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('timezone', 40)->default('Asia/Jakarta');
            $table->integer('initial_geofence_radius_m')->default(50);
            $table->string('status', 12)->default('aktif');
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
        });

        DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_status_chk CHECK (status IN ('aktif', 'ditangguhkan'))");
        DB::statement("ALTER TABLE companies ADD CONSTRAINT companies_radius_chk CHECK (initial_geofence_radius_m > 0)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
