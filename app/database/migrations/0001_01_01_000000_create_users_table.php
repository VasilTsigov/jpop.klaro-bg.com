<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // id = legacy UserID (keeps links and file paths intact)
            $table->unsignedInteger('id')->primary();
            $table->string('username', 100)->nullable();
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            // LEGACY: 4 duplicated emails exist -> not unique at DB level (see 03-plan.md §2)
            $table->string('email', 255)->nullable()->index();
            $table->string('password')->nullable();
            $table->string('legacy_sha1', 500)->nullable(); // cleared on first successful login
            $table->enum('role', ['author', 'reviewer', 'editor'])->default('author');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('verification_code', 20)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('fax', 20)->nullable();
            $table->string('country', 20)->nullable();
            $table->string('state', 20)->nullable();
            $table->string('city', 90)->nullable();
            $table->string('zip', 12)->nullable();
            $table->string('address', 100)->nullable();
            $table->string('address2', 50)->nullable();
            $table->string('registration_ip', 50)->nullable();
            $table->timestamp('registered_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
