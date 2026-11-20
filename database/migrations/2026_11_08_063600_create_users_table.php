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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->restrictOnDelete();
            $table->string('role', 10);
            $table->string('email', 190);
            $table->string('password_hash', 255);
            $table->string('status', 12)->default('aktif');
            $table->boolean('must_change_password')->default(false);
            $table->timestampTz('last_login_at')->nullable();
            $table->timestampTz('created_at')->useCurrent();
            $table->timestampTz('updated_at')->useCurrent();
            $table->index('company_id');
        });

        DB::statement("ALTER TABLE users ADD CONSTRAINT users_role_chk CHECK (role IN ('owner', 'karyawan'))");
        DB::statement("ALTER TABLE users ADD CONSTRAINT users_status_chk CHECK (status IN ('aktif', 'nonaktif', 'diarsipkan'))");
        // email tidak peka huruf besar/kecil: Budi@x.com dan budi@x.com dianggap sama
        DB::statement('CREATE UNIQUE INDEX users_email_lower_unique ON users (lower(email))');

        // Schema::create('password_reset_tokens', function (Blueprint $table) {
        //     $table->string('email')->primary();
        //     $table->string('token');
        //     $table->timestampTz('created_at')->nullable();
        // });

        // Schema::create('sessions', function (Blueprint $table) {
        //     $table->string('id')->primary();
        //     $table->foreignId('user_id')->nullable()->index();
        //     $table->string('ip_address', 45)->nullable();
        //     $table->text('user_agent')->nullable();
        //     $table->longText('payload');
        //     $table->integer('last_activity')->index();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        // Schema::dropIfExists('password_reset_tokens');
        // Schema::dropIfExists('sessions');
    }
};
