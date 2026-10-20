<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // id = legacy mscrID; legacy_id kept for comparison with the old dump
        Schema::create('manuscripts', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary();
            $table->unsignedInteger('legacy_id')->nullable()->unique();
            $table->unsignedInteger('code')->nullable()->unique(); // mscrCode; label "JPOP".code is derived
            $table->unsignedInteger('author_id')->nullable()->index(); // NULL = orphan author (34, 342, 343)
            $table->unsignedTinyInteger('status')->default(0); // 0 in_progress, 1 rejected, 2 published
            $table->text('title')->nullable();
            $table->text('abstract')->nullable();
            $table->text('keywords')->nullable();
            $table->text('cover_letter')->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->string('submitted_at_raw', 50)->nullable(); // LEGACY: mscrUpldDate was varchar
            $table->timestamp('corrected_at')->nullable();
            $table->string('corrected_state', 100)->nullable(); // mscrCorrected (meaning confirmed in 4.1)
            $table->string('editor_letter_file', 100)->nullable();
            $table->timestamp('editor_letter_at')->nullable();
            $table->timestamp('proof_at')->nullable();
            $table->timestamp('proof_editor_at')->nullable();
            $table->timestamps();
        });

        Schema::create('manuscript_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('manuscript_id')->index();
            $table->enum('type', ['manuscript', 'photo', 'graph', 'corrected_manuscript', 'corrected_photo',
                'corrected_graph', 'review', 'checklist', 'editor_letter', 'proof', 'proof_letter']);
            $table->unsignedTinyInteger('slot')->default(1);
            $table->string('path', 200);
            $table->string('original_name', 200)->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('manuscript_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('manuscript_id');
            $table->unsignedTinyInteger('slot'); // 1..3
            $table->unsignedInteger('reviewer_id')->nullable(); // dangling ids (438, 91, 126) -> NULL
            $table->string('reviewer_name', 150)->nullable();
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('deadline_at')->nullable();
            $table->enum('confirmed_state', ['pending', 'confirmed', 'declined'])->default('pending');
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('recommendation', ['Rejected', 'Major revision', 'Minor revision'])->nullable();
            $table->unsignedBigInteger('review_file_id')->nullable();
            $table->unsignedBigInteger('checklist_file_id')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['manuscript_id', 'slot']);
        });

        Schema::create('review_invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('manuscript_reviews')->cascadeOnDelete();
            $table->enum('action', ['agree', 'decline']);
            $table->string('token_hash', 64)->index();
            $table->timestamp('expires_at'); // default 30 days
            $table->timestamp('used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('manuscript_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('manuscript_id')->index();
            $table->string('event', 50);
            $table->unsignedInteger('actor_id')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('activity_log', function (Blueprint $table) {
            $table->unsignedInteger('id')->primary(); // legacy logID
            $table->string('user', 100);
            $table->string('ip', 100);
            $table->string('content', 100);
            $table->timestamp('logged_at')->nullable();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['settings', 'activity_log', 'manuscript_events', 'review_invitations',
            'manuscript_reviews', 'manuscript_files', 'manuscripts'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
