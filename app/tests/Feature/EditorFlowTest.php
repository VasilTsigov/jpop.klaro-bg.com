<?php

namespace Tests\Feature;

use App\Mail\EditorialLetter;
use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\ManuscriptReview;
use App\Models\ReviewInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditorFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $editor;

    private User $author;

    private User $reviewer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('legacy_files');
        Storage::fake('manuscripts');
        Mail::fake();
        $this->editor = User::factory()->create(['role' => 'editor']);
        $this->author = User::factory()->create(['role' => 'author', 'first_name' => 'Ann', 'last_name' => 'Author']);
        $this->reviewer = User::factory()->create(['role' => 'reviewer', 'first_name' => 'Rob', 'last_name' => 'Reviewer']);
        DB::table('settings')->insert(['key' => 'editor_emails', 'value' => 'desk@example.com']);
    }

    private function manuscript(array $extra = []): Manuscript
    {
        static $n = 100;
        $n++;

        return Manuscript::create($extra + [
            'id' => $n, 'code' => 1000 + $n, 'author_id' => $this->author->id, 'status' => 0,
            'title' => "Title $n", 'abstract' => 'abs', 'cover_letter' => 'cl', 'submitted_at' => now(),
        ]);
    }

    private function review(Manuscript $m, int $slot, array $extra = []): ManuscriptReview
    {
        return ManuscriptReview::create($extra + [
            'manuscript_id' => $m->id, 'slot' => $slot, 'reviewer_id' => $this->reviewer->id,
            'reviewer_name' => 'Rob Reviewer', 'invited_at' => now()->subDays(10), 'confirmed_state' => 'confirmed',
        ]);
    }

    private function sentTo(string $address, ?string $subject = null): bool
    {
        return Mail::queued(EditorialLetter::class, fn (EditorialLetter $l) => $l->hasTo($address)
            && ($subject === null || $l->mailSubject === $subject))->isNotEmpty();
    }

    public function test_only_editors_reach_the_desk(): void
    {
        $m = $this->manuscript();
        $urls = [
            ['get', route('editor.list', 'inprogress')],
            ['get', route('editor.assign', [$m, 1])],
            ['post', route('editor.assign', [$m, 1])],
            ['get', route('editor.status', $m)],
            ['post', route('editor.status', $m)], // LEGACY: mscr_Status had no guard at all
            ['post', route('editor.reject', $m)],
            ['get', route('editor.decision', $m)],
            ['get', route('editor.proof', $m)],
        ];
        foreach ([$this->author, $this->reviewer] as $user) {
            foreach ($urls as [$method, $url]) {
                $this->actingAs($user)->$method($url)->assertForbidden();
            }
        }
        foreach ($urls as [$method, $url]) {
            auth()->logout();
            $this->$method($url)->assertRedirect(route('login'));
        }
        $this->assertSame(0, (int) $m->fresh()->status);
    }

    public function test_lists_split_by_status_newest_first_without_orphans_and_colour_by_recommendations(): void
    {
        $a = $this->manuscript();
        $b = $this->manuscript();
        $orphan = $this->manuscript(['author_id' => null, 'title' => 'Orphan title']);
        $rejected = $this->manuscript(['status' => 1]);
        $published = $this->manuscript(['status' => 2]);
        $this->review($b, 1, ['recommendation' => 'Rejected']);
        $this->review($b, 2, ['recommendation' => 'Rejected']);
        $this->review($a, 1, ['recommendation' => 'Rejected']);
        $this->review($a, 2, ['recommendation' => 'Minor revision']);

        $r = $this->actingAs($this->editor)->get(route('editor.list', 'inprogress'))->assertOk();
        $r->assertSeeInOrder([$b->title, $a->title])->assertDontSee($orphan->title)
            ->assertDontSee($rejected->title)->assertDontSee($published->title);
        $this->assertStringContainsString('#FF0000', $r->getContent()); // b: two "Rejected"
        $this->assertSame('#FEED77', $a->load('reviews')->recommendation_color);
        $this->assertSame('#FF0000', $b->load('reviews')->recommendation_color);

        $this->get(route('editor.list', 'rejected'))->assertOk()->assertSee($rejected->title)->assertDontSee($a->title);
        $this->get(route('editor.list', 'published'))->assertOk()->assertSee($published->title)->assertDontSee($a->title);
        $this->get('/editor/lists/other')->assertNotFound();
    }

    public function test_assigning_a_reviewer_stores_the_slot_and_mails_one_time_links(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor)->post(route('editor.assign', [$m, 1]), ['reviewer_id' => $this->reviewer->id])
            ->assertRedirect(route('editor.list', 'inprogress'));

        $review = ManuscriptReview::where('manuscript_id', $m->id)->where('slot', 1)->firstOrFail();
        $this->assertSame($this->reviewer->id, $review->reviewer_id);
        $this->assertSame('Rob Reviewer', $review->reviewer_name);
        $this->assertSame('pending', $review->confirmed_state);
        $this->assertNotNull($review->invited_at);
        $this->assertSame(2, $review->invitations()->count());
        $this->assertDatabaseHas('manuscript_events', ['manuscript_id' => $m->id, 'event' => 'reviewer_assigned']);
        $this->assertDatabaseHas('activity_log', ['content' => 'Send To Reviewer No 1']);

        $this->assertTrue($this->sentTo($this->reviewer->email, "Manuscript {$m->code_label} to review"));
        $this->assertTrue($this->sentTo('desk@example.com')); // copy for the editors
        Mail::assertQueued(EditorialLetter::class, function (EditorialLetter $l) use ($m) {
            // the raw token is only in the mail, the database keeps its hash
            return $l->hasTo($this->reviewer->email)
                && str_contains($l->letterHtml, 'I agree to review this submission.')
                && str_contains($l->letterHtml, $m->title)
                && preg_match_all('#/review/([A-Za-z0-9]{48})#', $l->letterHtml, $t) === 2
                && ReviewInvitation::where('token_hash', ReviewInvitation::hash($t[1][0]))->exists();
        });
    }

    public function test_slot_two_and_three_invitations_keep_legacy_subjects_and_attachments(): void
    {
        $m = $this->manuscript();
        Storage::disk('manuscripts')->put('manuscripts/JPOP.docx', 'x');
        ManuscriptFile::create(['manuscript_id' => $m->id, 'type' => 'manuscript', 'slot' => 1, 'path' => 'manuscripts/JPOP.docx', 'disk' => 'manuscripts']);
        Storage::disk('legacy_files')->put('attach/Checklist.doc', 'c');
        Storage::disk('legacy_files')->put('attach/Instructions_for_reviewer.doc', 'i');

        $this->actingAs($this->editor);
        $this->post(route('editor.assign', [$m, 2]), ['reviewer_id' => $this->reviewer->id]);
        $this->post(route('editor.assign', [$m, 3]), ['reviewer_id' => $this->reviewer->id]);

        $this->assertTrue($this->sentTo($this->reviewer->email, "Manuscript{$m->code_label} to review")); // LEGACY: no space
        $this->assertTrue($this->sentTo($this->reviewer->email, "Manuscript {$m->code_label} for review"));
        Mail::assertQueued(EditorialLetter::class, fn (EditorialLetter $l) => $l->hasTo($this->reviewer->email)
            && count($l->attachments()) === 3);
    }

    public function test_only_reviewers_can_be_assigned_and_only_to_manuscripts_in_progress(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor)->post(route('editor.assign', [$m, 1]), ['reviewer_id' => $this->author->id])
            ->assertSessionHasErrors('reviewer_id');
        $closed = $this->manuscript(['status' => 1]);
        $this->post(route('editor.assign', [$closed, 1]), ['reviewer_id' => $this->reviewer->id])->assertSessionHasErrors();
        $this->assertSame(0, ManuscriptReview::count());
        Mail::assertNothingQueued();
    }

    public function test_agree_link_confirms_and_is_used_up_decline_empties_the_slot(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor);
        $links = function (int $slot) use ($m) {
            $this->post(route('editor.assign', [$m, $slot]), ['reviewer_id' => $this->reviewer->id]);
            $mail = Mail::queued(EditorialLetter::class, fn ($l) => $l->hasTo($this->reviewer->email))->last();
            preg_match_all('#/review/([A-Za-z0-9]{48})#', $mail->letterHtml, $t);

            return $t[1]; // [agree, decline]
        };

        [$agree, $decline] = $links(1);
        auth()->logout();
        $this->get(route('review.respond', $agree))->assertOk()->assertSee('Confirm');
        $this->assertSame('pending', $m->reviews()->first()->confirmed_state); // GET changes nothing
        $this->post(route('review.respond', $agree))->assertOk()->assertSee('confirmed');
        $review = $m->reviews()->where('slot', 1)->first();
        $this->assertSame('confirmed', $review->confirmed_state);
        $this->assertNotNull($review->confirmed_at);
        $this->post(route('review.respond', $decline))->assertNotFound(); // the pair is used up
        $this->get(route('review.respond', $agree))->assertOk()->assertSee('no longer valid');

        Mail::fake();
        $this->actingAs($this->editor);
        [, $decline2] = $links(2);
        auth()->logout();
        $this->post(route('review.respond', $decline2), ['info' => 'No time, sorry'])->assertOk()->assertSee('declined');
        $slot2 = $m->reviews()->where('slot', 2)->first();
        $this->assertSame('declined', $slot2->confirmed_state);
        $this->assertNull($slot2->reviewer_id);
        $this->assertNull($slot2->reviewer_name);
        $this->assertTrue($this->sentTo('desk@example.com', 'Message from Reviewer'));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => str_contains($l->letterHtml, 'No time, sorry'));
    }

    public function test_expired_link_is_refused(): void
    {
        $m = $this->manuscript();
        $review = $this->review($m, 1, ['confirmed_state' => 'pending']);
        $review->invitations()->create(['action' => 'agree', 'token_hash' => ReviewInvitation::hash('tok'), 'expires_at' => now()->subMinute()]);
        $this->post(route('review.respond', 'tok'))->assertNotFound();
        $this->assertSame('pending', $review->fresh()->confirmed_state);
    }

    public function test_resend_replaces_the_links_for_a_pending_invitation_only(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor)->post(route('editor.assign', [$m, 1]), ['reviewer_id' => $this->reviewer->id]);
        $old = ReviewInvitation::pluck('id')->all();

        $this->post(route('editor.resend', [$m, 1]))->assertRedirect();
        $this->assertSame(2, ReviewInvitation::count());
        $this->assertEmpty(array_intersect($old, ReviewInvitation::pluck('id')->all()));
        Mail::assertQueued(EditorialLetter::class, 2 + 1 + 1); // reviewer + desk copy, twice -> 4

        $m->reviews()->update(['confirmed_state' => 'confirmed']);
        $this->post(route('editor.resend', [$m, 1]))->assertNotFound();
    }

    public function test_reminder_is_mailed_to_the_reviewer_with_the_due_date_and_without_scripts(): void
    {
        $m = $this->manuscript();
        $this->review($m, 1, ['invited_at' => '2026-01-31 10:00:00']);

        $this->actingAs($this->editor)->get(route('editor.reminder', [$m, 1]))->assertOk()
            ->assertSee('03.03.2026'); // LEGACY: +1 month with PHP overflow
        $this->post(route('editor.reminder', [$m, 1]), ['reminder' => '<p>Hurry</p><script>alert(1)</script>'])
            ->assertRedirect(route('editor.list', 'inprogress'));

        $this->assertTrue($this->sentTo($this->reviewer->email, "Journal POP reminder late review {$m->code_label}"));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => $l->hasTo($this->reviewer->email) && str_contains($l->letterHtml, 'Hurry') && ! str_contains($l->letterHtml, '<script'));
        $this->get(route('editor.reminder', [$m, 2]))->assertNotFound(); // no reviewer in slot 2
    }

    public function test_decision_letter_is_stored_dated_and_mailed_to_the_author(): void
    {
        $m = $this->manuscript(['code' => 1797]);
        $this->actingAs($this->editor)->post(route('editor.decision', $m), ['letter' => UploadedFile::fake()->create('l.exe', 1)])
            ->assertSessionHasErrors('letter');

        $this->post(route('editor.decision', $m), ['letter' => UploadedFile::fake()->create('decision.docx', 5)])
            ->assertRedirect(route('editor.list', 'inprogress'));

        $file = $m->files()->where('type', 'editor_letter')->firstOrFail();
        $this->assertSame('editletter/JPOP1797_editor_letter.docx', $file->path);
        Storage::disk('manuscripts')->assertExists($file->path);
        $this->assertNotNull($m->fresh()->editor_letter_at);
        $this->assertTrue($this->sentTo($this->author->email, 'Journal POP - Letter from the editor'));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => $l->hasTo($this->author->email) && $l->files === [['manuscripts', $file->path]]);

        // the author may fetch it, the other author may not (ManuscriptPolicy)
        $this->actingAs($this->author)->get(route('files.show', $file))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'author']))->get(route('files.show', $file))->assertForbidden();

        $this->actingAs($this->editor)->post(route('editor.decision', $m), ['letter' => UploadedFile::fake()->create('again.pdf', 5)]);
        $this->assertSame(1, $m->files()->where('type', 'editor_letter')->count()); // replaced, not duplicated
    }

    public function test_direct_reject_sets_status_and_writes_to_the_author(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor)->get(route('editor.reject', $m))->assertOk()->assertSee($m->code_label);
        $this->post(route('editor.reject', $m))->assertRedirect(route('editor.list', 'inprogress'));

        $this->assertSame(1, (int) $m->fresh()->status);
        $this->assertTrue($this->sentTo($this->author->email, 'Rejected Manuscript'));
        $this->assertDatabaseHas('manuscript_events', ['manuscript_id' => $m->id, 'event' => 'rejected_directly']);
        $this->get(route('editor.list', 'rejected'))->assertSee($m->title);
    }

    public function test_status_change_does_not_send_mail_and_rejects_bad_values(): void
    {
        $m = $this->manuscript();
        $this->actingAs($this->editor)->post(route('editor.status', $m), ['status' => 2])->assertRedirect();
        $this->assertSame(2, (int) $m->fresh()->status);
        $this->post(route('editor.status', $m), ['status' => 7])->assertSessionHasErrors('status');
        $this->post(route('editor.status', $m), ['status' => 0]);
        $this->assertSame(0, (int) $m->fresh()->status);
        Mail::assertNothingQueued();
    }

    public function test_letters_to_reviewers_carry_the_other_recommendations_and_the_decision_letter(): void
    {
        $m = $this->manuscript();
        $second = User::factory()->create(['role' => 'reviewer', 'first_name' => 'Sue', 'last_name' => 'Second']);
        $this->review($m, 1, ['recommendation' => 'Rejected']);
        $this->review($m, 2, ['recommendation' => 'Major revision', 'reviewer_id' => $second->id, 'reviewer_name' => 'Sue Second']);
        $this->review($m, 3, ['reviewer_id' => null, 'reviewer_name' => null, 'confirmed_state' => 'declined']);
        Storage::disk('manuscripts')->put('editletter/x.docx', 'x');
        ManuscriptFile::create(['manuscript_id' => $m->id, 'type' => 'editor_letter', 'slot' => 1, 'path' => 'editletter/x.docx', 'disk' => 'manuscripts']);

        $page = $this->actingAs($this->editor)->get(route('editor.status', $m))->assertOk();
        $page->assertSee('We would like to remind you that you have recommended &quot;Rejected&quot;', false)
            ->assertSee('Another reviewer suggested &quot;Major revision&quot;', false);

        $letters = [1 => '<p>Hello Rob</p>', 2 => '<p>Hello Sue</p>', 3 => '<p>nobody</p>'];
        $this->post(route('editor.letters', $m), ['letter' => $letters])->assertRedirect();

        $this->assertTrue($this->sentTo($this->reviewer->email, 'Letter to Reviewer'));
        $this->assertTrue($this->sentTo($second->email, 'Letter to Reviewer'));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => $l->hasTo($second->email) && $l->files === [['manuscripts', 'editletter/x.docx']]);
        Mail::assertNotQueued(EditorialLetter::class, fn ($l) => str_contains($l->letterHtml, 'nobody'));
    }

    public function test_proof_upload_stores_both_files_and_mails_the_author(): void
    {
        $m = $this->manuscript(['code' => 1800]);
        $this->actingAs($this->editor)->post(route('editor.proof', $m), [
            'proof' => UploadedFile::fake()->create('p.pdf', 10),
            'proof_letter' => UploadedFile::fake()->create('l.docx', 2),
        ])->assertRedirect(route('editor.list', 'inprogress'));

        $paths = $m->files()->pluck('path', 'type')->all();
        $this->assertSame('proof/JPOP1800_proof.pdf', $paths['proof']);
        $this->assertSame('editletter/JPOP1800_proof_letter.docx', $paths['proof_letter']);
        $m->refresh();
        $this->assertNotNull($m->proof_at);
        $this->assertNotNull($m->proof_editor_at);
        $this->assertTrue($this->sentTo($this->author->email, 'Journal POP - Proof File'));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => $l->hasTo($this->author->email) && count($l->files) === 2);

        $this->post(route('editor.proof', $m), ['proof' => UploadedFile::fake()->create('p.pdf', 10)])
            ->assertSessionHasErrors('proof_letter');
    }
}
