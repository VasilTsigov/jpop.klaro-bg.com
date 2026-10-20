<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_tables_from_the_migration_plan_exist(): void
    {
        foreach (['users', 'manuscripts', 'manuscript_files', 'manuscript_reviews',
            'review_invitations', 'manuscript_events', 'activity_log', 'settings'] as $table) {
            $this->assertTrue(Schema::hasTable($table), "missing table $table");
        }
    }

    public function test_legacy_user_id_is_kept_and_duplicate_emails_are_allowed(): void
    {
        User::factory()->create(['id' => 438, 'email' => 'dup@example.com']);
        User::factory()->create(['id' => 439, 'email' => 'dup@example.com']);

        $this->assertSame(438, User::find(438)->id);
        $this->assertSame(2, User::where('email', 'dup@example.com')->count());
    }
}
