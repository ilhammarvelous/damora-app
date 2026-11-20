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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('type', 30);
            $table->string('title', 150);
            $table->text('body');
            $table->jsonb('payload')->nullable();
            $table->timestampTz('scheduled_at');
            $table->timestampTz('sent_at')->nullable();
            $table->timestampTz('read_at')->nullable();
            $table->string('action', 20)->nullable();
            $table->timestampTz('action_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index(['user_id', 'scheduled_at']);
        });

        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_type_chk CHECK (type IN ('pengingat_pulang', 'keputusan_izin', 'penugasan_baru'))");
        DB::statement("ALTER TABLE notifications ADD CONSTRAINT notifications_action_chk CHECK (action IN ('absen_pulang', 'lanjut_lembur'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
