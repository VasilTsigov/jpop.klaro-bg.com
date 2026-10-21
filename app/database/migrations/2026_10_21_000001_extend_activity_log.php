<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Logs v2 (docs/03-plan.md §9): the legacy rows stay (category 'legacy'), new rows carry category, level, actor,
 * manuscript, request and a JSON context. `id` becomes auto-increment (the legacy logID was assigned by hand).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->unsignedInteger('id', true)->change();
            $table->string('content', 255)->change();
        });
        Schema::table('activity_log', function (Blueprint $table) {
            $table->string('category', 20)->default('legacy')->index();
            $table->string('level', 10)->default('info')->index();
            $table->unsignedInteger('user_id')->nullable()->index();
            $table->string('role', 10)->nullable();
            $table->unsignedInteger('manuscript_id')->nullable()->index();
            $table->string('method', 8)->nullable();
            $table->string('url', 255)->nullable();
            $table->unsignedSmallInteger('status')->nullable();
            $table->json('context')->nullable();
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table) {
            $table->dropIndex(['logged_at']);
            $table->dropColumn(['category', 'level', 'user_id', 'role', 'manuscript_id', 'method', 'url', 'status', 'context']);
        });
    }
};
