<?php

namespace Tests\Feature;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\ManuscriptReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $author;
    private User $other;
    private User $reviewer1;
    private User $reviewer2;
    private User $reviewer3;
    private User $editor;
    private Manuscript $m;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('legacy_files');
        Storage::fake('manuscripts');

        $this->author = User::factory()->create(['role' => 'author']);
        $this->other = User::factory()->create(['role' => 'author']);
        $this->reviewer1 = User::factory()->create(['role' => 'reviewer']);
        $this->reviewer2 = User::factory()->create(['role' => 'reviewer']);
        $this->reviewer3 = User::factory()->create(['role' => 'reviewer']); // not assigned
        $this->editor = User::factory()->create(['role' => 'editor']);

        $this->m = Manuscript::create(['id' => 1, 'code' => 1, 'author_id' => $this->author->id]);
        ManuscriptReview::create(['manuscript_id' => 1, 'slot' => 1, 'reviewer_id' => $this->reviewer1->id, 'confirmed_state' => 'confirmed']);
        ManuscriptReview::create(['manuscript_id' => 1, 'slot' => 2, 'reviewer_id' => $this->reviewer2->id, 'confirmed_state' => 'declined']);
    }

    private function file(string $type, int $slot = 1, string $disk = 'legacy_files', bool $onDisk = true): ManuscriptFile
    {
        $path = "$type/JPOP1-$slot.doc";
        if ($onDisk) {
            Storage::disk($disk)->put($path, "content $type $slot");
        }

        return ManuscriptFile::create([
            'manuscript_id' => 1, 'type' => $type, 'slot' => $slot, 'path' => $path,
            'disk' => $disk, 'original_name' => "JPOP1-$slot.doc",
        ]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $f = $this->file('manuscript');

        $this->get(route('files.show', $f))->assertRedirect(route('login'));
    }

    public function test_author_downloads_own_submission_and_editor_letter_but_not_reviews(): void
    {
        $this->actingAs($this->author);

        $this->get(route('files.show', $this->file('manuscript')))->assertOk()
            ->assertDownload('JPOP1-1.doc');
        $this->get(route('files.show', $this->file('editor_letter')))->assertOk();
        $this->get(route('files.show', $this->file('review')))->assertForbidden();
        $this->get(route('files.show', $this->file('checklist')))->assertForbidden();
    }

    public function test_other_author_cannot_download(): void
    {
        $f = $this->file('manuscript');

        $this->actingAs($this->other)->get(route('files.show', $f))->assertForbidden();
    }

    public function test_orphan_manuscript_is_editor_only(): void
    {
        $this->m->update(['author_id' => null]);
        $f = $this->file('manuscript');

        $this->actingAs($this->author)->get(route('files.show', $f))->assertForbidden();
        $this->actingAs($this->editor)->get(route('files.show', $f))->assertOk();
    }

    public function test_assigned_reviewer_gets_submission_and_only_own_review(): void
    {
        $this->actingAs($this->reviewer1);

        $this->get(route('files.show', $this->file('manuscript')))->assertOk();
        $this->get(route('files.show', $this->file('photo', 2)))->assertOk();
        $this->get(route('files.show', $this->file('review', 1)))->assertOk();
        $this->get(route('files.show', $this->file('checklist', 1)))->assertOk();
        $this->get(route('files.show', $this->file('review', 2)))->assertForbidden();
        $this->get(route('files.show', $this->file('editor_letter')))->assertForbidden();
        $this->get(route('files.show', $this->file('proof')))->assertForbidden();
    }

    public function test_unassigned_and_declined_reviewers_are_refused(): void
    {
        $f = $this->file('manuscript');

        $this->actingAs($this->reviewer3)->get(route('files.show', $f))->assertForbidden();
        $this->actingAs($this->reviewer2)->get(route('files.show', $f))->assertForbidden();
    }

    public function test_editor_downloads_everything(): void
    {
        $this->actingAs($this->editor);

        foreach (['manuscript', 'review', 'checklist', 'editor_letter', 'proof', 'proof_letter'] as $type) {
            $this->get(route('files.show', $this->file($type)))->assertOk();
        }
    }

    public function test_new_uploads_are_read_from_manuscripts_disk(): void
    {
        $f = $this->file('manuscript', 1, 'manuscripts');

        $this->actingAs($this->author)->get(route('files.show', $f))->assertOk();
    }

    public function test_missing_file_on_disk_is_404(): void
    {
        $f = $this->file('manuscript', 1, 'legacy_files', false);

        $this->actingAs($this->editor)->get(route('files.show', $f))->assertNotFound();
    }

    public function test_unknown_disk_value_is_404_and_traversal_is_not_served(): void
    {
        $f = $this->file('manuscript');
        $f->update(['disk' => 'local']);
        $this->actingAs($this->editor)->get(route('files.show', $f))->assertNotFound();

        $f->update(['disk' => 'legacy_files', 'path' => '../../.env']);
        $this->get(route('files.show', $f))->assertNotFound();
    }

    public function test_direct_urls_to_files_are_not_served(): void
    {
        $this->file('manuscript');
        $this->actingAs($this->editor);

        foreach (['/files/manuscripts/JPOP1.doc', '/storage/manuscripts/JPOP1-1.doc', '/manuscripts/JPOP1-1.doc'] as $url) {
            $this->get($url)->assertNotFound();
        }
        $this->assertDirectoryDoesNotExist(public_path('files'));
    }
}
