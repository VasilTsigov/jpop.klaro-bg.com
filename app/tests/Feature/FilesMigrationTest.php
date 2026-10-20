<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FilesMigrationTest extends TestCase
{
    use RefreshDatabase;

    private string $src;
    private string $dst;

    protected function setUp(): void
    {
        parent::setUp();
        $base = sys_get_temp_dir() . '/jpopfiles' . uniqid();
        $this->src = "$base/src";
        $this->dst = "$base/dst";
        mkdir("{$this->src}/manuscripts", 0777, true);
        mkdir("{$this->src}/attach", 0777, true);
        mkdir("{$this->src}/photographs", 0777, true);
        file_put_contents("{$this->src}/manuscripts/JPOP1.doc", 'abc');
        file_put_contents("{$this->src}/attach/Checklist.doc", 'chk');
        file_put_contents("{$this->src}/photographs/unused.jpg", 'zzzz');

        $u = User::factory()->create(['role' => 'author']);
        Manuscript::create(['id' => 1, 'code' => 1, 'author_id' => $u->id]);
        foreach (['manuscripts/JPOP1.doc', 'manuscripts/JPOP2.doc'] as $p) {
            DB::table('manuscript_files')->insert(['manuscript_id' => 1, 'type' => 'manuscript', 'path' => $p,
                'original_name' => basename($p), 'disk' => 'legacy_files']);
        }
    }

    public function test_dry_run_copies_nothing(): void
    {
        $this->artisan('jpop:files', ['--from' => $this->src, '--to' => $this->dst])
            ->expectsOutputToContain('needed 3: present 2 (0 MB), missing 1')
            ->expectsOutputToContain('not referenced (NOT copied): 1 files')
            ->assertSuccessful();
        $this->assertDirectoryDoesNotExist($this->dst);
    }

    public function test_copy_is_idempotent_and_skips_unreferenced(): void
    {
        $args = ['--from' => $this->src, '--to' => $this->dst, '--copy' => true];
        $this->artisan('jpop:files', $args)->expectsOutputToContain('copied 2, failed 0')->assertSuccessful();
        $this->assertFileExists("{$this->dst}/manuscripts/JPOP1.doc");
        $this->assertFileExists("{$this->dst}/attach/Checklist.doc");
        $this->assertFileDoesNotExist("{$this->dst}/photographs/unused.jpg");

        $this->artisan('jpop:files', $args)->expectsOutputToContain('copied 0, failed 0')->assertSuccessful();
        $this->artisan('jpop:files', ['--from' => $this->src, '--to' => $this->dst, '--verify' => true])->assertSuccessful();

        file_put_contents("{$this->dst}/manuscripts/JPOP1.doc", 'truncated!');
        $this->artisan('jpop:files', ['--from' => $this->src, '--to' => $this->dst, '--verify' => true])->assertFailed();
    }
}
