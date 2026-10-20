<?php

namespace Tests\Feature;

use App\Mail\EditorialLetter;
use App\Models\Manuscript;
use App\Models\ManuscriptReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    private function manuscript(User $author, array $extra = []): Manuscript
    {
        static $n = 500;
        $n++;

        return Manuscript::create($extra + [
            'id' => $n, 'code' => 2000 + $n, 'author_id' => $author->id, 'status' => 0, 'title' => "Roses $n",
            'abstract' => 'about petals', 'cover_letter' => 'dear editor', 'keywords' => 'rose, cutting', 'submitted_at' => now(),
        ]);
    }

    public function test_public_pages_open_without_login(): void
    {
        $this->get(route('instructions'))->assertOk()->assertSee('How to submit manuscript');
        $this->get(route('issues'))->assertOk();
        $this->get(route('contacts'))->assertOk()->assertSee('Contact Form');
        $this->get(route('contacts.thanks'))->assertOk()->assertSee('Thank you for your message.');
    }

    public function test_consent_file_is_public_and_404_when_missing(): void
    {
        Storage::fake('legacy_files');
        $this->get(route('instructions.consent'))->assertNotFound();
        Storage::disk('legacy_files')->put('attach/CONSENT_for_AUTHORS.doc', 'x');
        $this->get(route('instructions.consent'))->assertOk();
    }

    public function test_contact_form_mails_the_editors_and_rejects_bots(): void
    {
        Mail::fake();
        DB::table('settings')->insert(['key' => 'editor_emails', 'value' => 'desk@example.com']);
        $data = ['email' => 'visitor@example.com', 'name' => 'Vi', 'message' => "hello\nworld"];

        $this->post(route('contacts'), $data + ['website' => 'http://spam'])->assertSessionHasErrors('website');
        $this->post(route('contacts'), ['email' => 'bad'] + $data)->assertSessionHasErrors('email');
        Mail::assertNothingQueued();

        $this->post(route('contacts'), $data)->assertRedirect(route('contacts.thanks'));
        Mail::assertQueued(EditorialLetter::class, fn ($l) => $l->hasTo('desk@example.com')
            && $l->mailSubject === 'Message from contact form' && str_contains($l->letterHtml, 'visitor@example.com'));
    }

    public function test_search_is_for_editors_only_and_finds_by_author_and_text(): void
    {
        $ann = User::factory()->create(['role' => 'author', 'first_name' => 'Ann', 'last_name' => 'Gardener']);
        $bob = User::factory()->create(['role' => 'author', 'first_name' => 'Bob', 'last_name' => 'Other']);
        $a = $this->manuscript($ann);
        $b = $this->manuscript($bob, ['title' => 'Tulips', 'abstract' => '100% bulbs', 'keywords' => 'x']);
        $editor = User::factory()->create(['role' => 'editor']);

        $this->get(route('editor.search'))->assertRedirect(route('login'));
        $this->actingAs($ann)->get(route('editor.search'))->assertForbidden();

        $this->actingAs($editor);
        $this->get(route('editor.search'))->assertOk()->assertDontSee('Search Result');
        $this->get(route('editor.search', ['q' => 'ann gardener']))->assertSee($a->title)->assertDontSee('Tulips');
        $this->get(route('editor.search', ['q' => 'cutting']))->assertSee($a->title)->assertDontSee('Tulips'); // keywords
        $this->get(route('editor.search', ['q' => '100%']))->assertSee('Tulips')->assertDontSee($a->title); // % is literal
        $this->get(route('editor.search', ['q' => 'nothing-like-this']))->assertSee('No search result');
        $this->assertDatabaseHas('activity_log', ['content' => 'Enter main search site']);
    }

    public function test_abstract_and_cover_letter_visibility(): void
    {
        $owner = User::factory()->create(['role' => 'author']);
        $other = User::factory()->create(['role' => 'author']);
        $editor = User::factory()->create(['role' => 'editor']);
        $assigned = User::factory()->create(['role' => 'reviewer']);
        $stranger = User::factory()->create(['role' => 'reviewer']);
        $declined = User::factory()->create(['role' => 'reviewer']);
        $m = $this->manuscript($owner);
        foreach ([[1, $assigned, 'confirmed'], [2, $declined, 'declined']] as [$slot, $u, $state]) {
            ManuscriptReview::create([
                'manuscript_id' => $m->id, 'slot' => $slot, 'reviewer_id' => $u->id, 'reviewer_name' => 'R',
                'invited_at' => now(), 'confirmed_state' => $state,
            ]);
        }

        $this->get(route('manuscripts.text', [$m, 'abstract']))->assertRedirect(route('login'));
        foreach ([$owner, $editor, $assigned] as $u) {
            $this->actingAs($u)->get(route('manuscripts.text', [$m, 'abstract']))->assertOk()->assertSee('about petals');
            $this->actingAs($u)->get(route('manuscripts.text', [$m, 'cover-letter']))->assertOk()->assertSee('dear editor');
        }
        foreach ([$other, $stranger, $declined] as $u) { // LEGACY: any logged-in user could open any id
            $this->actingAs($u)->get(route('manuscripts.text', [$m, 'abstract']))->assertForbidden();
        }
        $this->actingAs($editor)->get('/manuscripts/999/abstract')->assertNotFound();
    }
}
