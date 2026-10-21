<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\User;
use App\Support\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LogsV2Test extends TestCase
{
    use RefreshDatabase;

    private function editor(): User
    {
        return User::factory()->create(['role' => 'editor']);
    }

    public function test_login_logout_and_failed_login_are_logged(): void
    {
        $u = User::factory()->create(['role' => 'author', 'email' => 'a@example.com', 'password' => 'secret1', 'email_verified_at' => now()]);

        $this->post('/users/login', ['email' => 'a@example.com', 'password' => 'wrong-pass']);
        $row = DB::table('activity_log')->where('category', 'auth')->first();
        $this->assertSame('warning', $row->level);
        $this->assertSame('(guest)', $row->user);
        $this->assertStringNotContainsString('wrong-pass', json_encode($row));

        $this->post('/users/login', ['email' => 'a@example.com', 'password' => 'secret1']);
        $this->assertDatabaseHas('activity_log', ['category' => 'auth', 'content' => 'Login', 'user_id' => $u->id, 'role' => 'author']);
        $this->post('/users/logout');
        $this->assertDatabaseHas('activity_log', ['category' => 'auth', 'content' => 'Logout', 'user_id' => $u->id]);
    }

    public function test_pages_opened_are_logged_once_per_minute_and_the_journal_itself_is_not(): void
    {
        $editor = $this->editor();
        $this->actingAs($editor)->get('/editor/users')->assertOk();
        $this->get('/editor/users')->assertOk();
        $this->get('/editor/logs')->assertOk();

        $this->assertSame(1, DB::table('activity_log')->where('category', 'view')->where('content', 'Open Users')->count());
        $this->assertSame(0, DB::table('activity_log')->where('content', 'like', '%logs%')->count());
    }

    public function test_refused_requests_are_logged_as_security(): void
    {
        $author = User::factory()->create(['role' => 'author']);
        $this->actingAs($author)->get('/editor/users')->assertForbidden();

        $row = DB::table('activity_log')->where('category', 'security')->first();
        $this->assertSame('warning', $row->level);
        $this->assertSame(403, (int) $row->status);
        $this->assertSame('/editor/users', $row->url);
    }

    public function test_a_refused_upload_is_logged_with_the_file_and_the_reason(): void
    {
        Storage::fake('manuscripts');
        $author = User::factory()->create(['role' => 'author']);
        $this->actingAs($author)->post('/manuscripts/submit', [
            'title' => 't', 'abstract' => 'a', 'keywords' => 'k', 'cover_letter' => 'c',
            'manuscript' => UploadedFile::fake()->create('virus.exe', 10),
        ]);

        $row = DB::table('activity_log')->where('category', 'upload')->first();
        $this->assertNotNull($row);
        $this->assertSame('warning', $row->level);
        $this->assertStringContainsString('virus.exe', $row->context);
    }

    public function test_an_unhandled_exception_is_logged_as_error(): void
    {
        ActivityLog::exception(new \RuntimeException('boom'));
        $row = DB::table('activity_log')->where('category', 'error')->first();
        $this->assertSame('error', $row->level);
        $this->assertStringContainsString('boom', $row->content);
    }

    public function test_filters_and_csv_export(): void
    {
        $editor = $this->editor();
        ActivityLog::event('auth', 'Login failed: wrong email or password', 'warning');
        ActivityLog::event('upload', 'Submit manuscript', 'info', ['files' => [['name' => 'a.docx', 'size' => 2048]]], 7);
        ActivityLog::event('upload', '=cmd()', 'info');

        $this->actingAs($editor)->get('/editor/logs?category=auth&level=warning')->assertOk()->assertSee('Login failed')->assertDontSee('Submit manuscript');
        $this->get('/editor/logs?manuscript=7')->assertOk()->assertSee('a.docx')->assertDontSee('Login failed');
        $this->get('/editor/logs?category=nope')->assertSessionHasErrors('category');

        $csv = $this->get('/editor/logs?category=upload&export=csv')->assertOk()->streamedContent();
        $this->assertStringContainsString('Submit manuscript', $csv);
        $this->assertStringContainsString("'=cmd()", $csv);
        $this->assertStringNotContainsString('Login failed', $csv);
    }

    public function test_prune_archives_and_deletes_only_what_is_due_and_never_legacy(): void
    {
        $old = fn ($cat, $days, $content) => DB::table('activity_log')->insert([
            'user' => 'u', 'ip' => '1', 'content' => $content, 'category' => $cat, 'level' => 'info', 'logged_at' => now()->subDays($days),
        ]);
        $old('view', 100, 'old view');
        $old('view', 10, 'fresh view');
        $old('auth', 100, 'auth within a year');
        $old('legacy', 5000, 'legacy forever');
        $dir = storage_path('app/log-archive');
        File::deleteDirectory($dir);

        $this->artisan('jpop:logs:prune --dry-run')->assertSuccessful();
        $this->assertSame(4, DB::table('activity_log')->count());

        $this->artisan('jpop:logs:prune')->assertSuccessful();
        $contents = DB::table('activity_log')->pluck('content')->all();
        $this->assertNotContains('old view', $contents);
        $this->assertContains('fresh view', $contents);
        $this->assertContains('auth within a year', $contents);
        $this->assertContains('legacy forever', $contents);
        $this->assertDatabaseHas('activity_log', ['category' => 'system']);

        $files = glob("$dir/*.csv.gz");
        $this->assertCount(1, $files);
        $this->assertStringContainsString('old view', gzdecode(file_get_contents($files[0])));
        File::deleteDirectory($dir);
    }

    public function test_actions_carry_the_manuscript(): void
    {
        $editor = $this->editor();
        $author = User::factory()->create(['role' => 'author']);
        $m = Manuscript::create(['id' => 5, 'code' => 1005, 'author_id' => $author->id, 'status' => 0, 'title' => 't', 'abstract' => 'a', 'cover_letter' => 'c', 'submitted_at' => now()]);

        $this->actingAs($editor)->post("/editor/manuscripts/{$m->id}/status", ['status' => 2]);
        $this->assertDatabaseHas('activity_log', ['content' => 'Change Status', 'manuscript_id' => 5, 'category' => 'editorial']);
    }
}
