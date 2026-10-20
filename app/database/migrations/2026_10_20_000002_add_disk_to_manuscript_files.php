<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('manuscript_files', function (Blueprint $table) {
            // legacy_files = old files/ tree (imported rows); manuscripts = new uploads
            $table->string('disk', 30)->default('legacy_files')->after('path');
        });
    }

    public function down(): void
    {
        Schema::table('manuscript_files', fn (Blueprint $table) => $table->dropColumn('disk'));
    }
};
