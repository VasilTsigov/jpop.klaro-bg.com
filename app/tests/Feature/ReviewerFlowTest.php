<?php

namespace Tests\Feature;

use App\Mail\EditorialLetter;
use App\Models\Manuscript;
use App\Models\ManuscriptReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReviewerFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    private User $reviewer;

    private User $other;

    private Manuscript $m;

    private ManuscriptReview $review;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('legacy_files');
        Storage::fake('manuscripts');
        Mail::fake();
        $this->author = User::factory()->create(['role' => 'author', 'first_name' => 'Ann', 'last_name' => 'Author']);
        $this->reviewer = User::factory()->create(['role' => 'reviewer']);
        $this->other = User::factory()->create(['role' => 'reviewer']);
        DB::table('settings')->insert(['key' => 'editor_emails', 'value' => 'desk@example.com']);
        $this->m = Manuscript::create([
            'id' => 501, 'code' => 1501, 'author_id' => $this->author->id, 'status' => 0, 'title' => 'T',
            'abstract' => 'a', 'cover_letter' => 'c', 'submitted_at' => now(),
        ]);
        $this->review = ManuscriptReview::create([
            'manuscript_id' => 501, 'slot' => 2, 'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => 'Rob Reviewer', 'invited_at' => now(), 'confirmed_state' => 'confirmed',
        ]);
        ManuscriptReview::create(['manuscript_id' => 501, 'slot' => 1, 'reviewer_id' => $this->other->id, 'reviewer_name' => 'Zed', 'confirmed_state' => 'confirmed']);
    }

    private function payload(array $extra = []): array
    {
        return $extra + [
            'review_file' => UploadedFile::fake()->create('rev.docx', 20),
            'checklist_file' => UploadedFile::fake()->create('chk.doc', 20),
            'recommendation' => 'Major revision',
        ];
    }

    public function test_list_shows_only_own_assignments(): void
    {
        $this->actingAs($this->reviewer)->get(route('reviews.index'))->assertOk()->assertSee('JPOP1501')
            ->assertSee(route('reviews.upload', $this->review));
        $this->actingAs($this->author)->get(route('reviews.index'))->assertForbidden();
        $this->actingAs(User::factory()->create(['role' => 'reviewer']))->get(route('reviews.index'))->assertOk()->assertDontSee('JPOP1501');
    }

    public function test_upload_stores_files_recommendation_and_sends_letters(): void
    {
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload())
            ->assertRedirect(route('reviews.index'));

        $r = $this->review->refresh();
        $this->assertSame('Major revision', $r->recommendation);
        $this->assertNotNull($r->reviewed_at);
        Storage::disk('manuscripts')->assertExists('reviewered/JPOP1501(reviewer2).docx');
        Storage::disk('manuscripts')->assertExists('checklist/JPOP1501(checklist-reviewer2).doc');
        $this->assertSame('review', $this->m->files()->find($r->review_file_id)->type);
        $this->assertDatabaseHas('manuscript_events', ['manuscript_id' => 501, 'event' => 'review_uploaded']);

        $to = fn ($a) => Mail::sent(EditorialLetter::class, fn ($l) => $l->hasTo($a));
        $this->assertCount(1, $to($this->author->email));
        $this->assertCount(1, $to('desk@example.com'));
        $this->assertSame('Reviewer Rob Reviewer uploads file', $to('desk@example.com')->first()->mailSubject);
        $this->assertEmpty($to($this->author->email)->first()->files); // authors do not get the checklist
    }

    public function test_reupload_replaces_and_other_reviewers_are_refused(): void
    {
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload())->assertRedirect();
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload(['recommendation' => 'Rejected']))->assertRedirect();
        $this->assertSame(1, $this->m->files()->where('type', 'review')->where('slot', 2)->count());
        $this->assertSame('Rejected', $this->review->refresh()->recommendation);

        $this->actingAs($this->other)->get(route('reviews.upload', $this->review))->assertForbidden();
        $this->actingAs($this->other)->post(route('reviews.upload', $this->review), $this->payload())->assertForbidden();
        $this->actingAs($this->author)->get(route('reviews.upload', $this->review))->assertForbidden();
    }

    public function test_declined_slot_cannot_upload_and_validation_applies(): void
    {
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload(['recommendation' => 'Nope']))
            ->assertSessionHasErrors('recommendation');
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload(['checklist_file' => UploadedFile::fake()->create('x.exe', 5)]))
            ->assertSessionHasErrors('checklist_file');
        $this->assertNull($this->review->refresh()->reviewed_at);

        $this->review->update(['confirmed_state' => 'declined']);
        $this->actingAs($this->reviewer)->get(route('reviews.upload', $this->review))->assertForbidden();
    }

    public function test_reviewer_downloads_own_review_but_not_the_others(): void
    {
        $this->actingAs($this->reviewer)->post(route('reviews.upload', $this->review), $this->payload());
        $this->actingAs($this->other)->post(route('reviews.upload', ManuscriptReview::where('slot', 1)->first()), $this->payload());
        $mine = $this->m->files()->where('type', 'review')->where('slot', 2)->first();
        $theirs = $this->m->files()->where('type', 'review')->where('slot', 1)->first();

        $this->actingAs($this->reviewer)->get(route('files.show', $mine))->assertOk();
        $this->actingAs($this->reviewer)->get(route('files.show', $theirs))->assertForbidden();
        $this->actingAs($this->author)->get(route('files.show', $mine))->assertForbidden();
    }

    public function test_help_files(): void
    {
        Storage::disk('legacy_files')->put('attach/Checklist.doc', 'x');
        $this->actingAs($this->reviewer)->get(route('reviews.help', 'checklist'))->assertOk();
        $this->actingAs($this->reviewer)->get(route('reviews.help', 'instructions'))->assertNotFound();
        $this->actingAs($this->author)->get(route('reviews.help', 'checklist'))->assertForbidden();
    }
}
