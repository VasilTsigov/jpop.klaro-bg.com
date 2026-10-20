<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/** Counts come from docs/02-characterization.md; needs the dump loaded in `jpop_legacy` (`jpop:import --dump=...`). */
class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        try {
            DB::connection('legacy')->table('mnscrpts')->count();
        } catch (\Throwable) {
            $this->markTestSkipped('legacy staging DB is not available');
        }
        $this->artisan('jpop:import')->assertExitCode(0);
    }

    public function test_row_counts_match_the_legacy_dump(): void
    {
        $this->assertSame(1347, DB::table('manuscripts')->count());
        $this->assertSame(1164, DB::table('users')->count()); // 1369 - 205 level-0
        $this->assertSame(7217, DB::table('activity_log')->count());
    }

    public function test_status_and_role_distribution(): void
    {
        $this->assertSame([0 => 9, 1 => 1121, 2 => 217],
            DB::table('manuscripts')->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status')->all());
        $this->assertSame(['author' => 1082, 'editor' => 5, 'reviewer' => 77],
            DB::table('users')->selectRaw('role, count(*) c')->groupBy('role')->pluck('c', 'role')->all());
    }

    public function test_code_is_kept_and_matches_legacy_label(): void
    {
        $legacy = DB::connection('legacy')->table('mnscrpts');
        $this->assertSame(0, (clone $legacy)->whereRaw("mscrCodeU <> CONCAT('JPOP', mscrCode)")->count());
        $this->assertSame(
            (clone $legacy)->max('mscrCode'),
            (int) DB::table('manuscripts')->max('code'),
        );
    }

    public function test_reviewers_per_slot_and_orphan_authors(): void
    {
        $this->assertSame([1 => 442, 2 => 420, 3 => 150],
            DB::table('manuscript_reviews')->selectRaw('slot, count(reviewer_id) c')->groupBy('slot')->pluck('c', 'slot')->all());
        $this->assertSame(3, DB::table('manuscripts')->whereNull('author_id')->count());
    }

    public function test_import_is_idempotent(): void
    {
        $this->artisan('jpop:import')->assertExitCode(0);
        $this->assertSame(1347, DB::table('manuscripts')->count());
        $this->assertSame(1164, DB::table('users')->count());
    }
}
