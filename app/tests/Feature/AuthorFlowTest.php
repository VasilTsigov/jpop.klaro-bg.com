<?php

namespace Tests\Feature;

use App\Mail\CorrectedUploaded;
use App\Mail\SubmissionNotice;
use App\Mail\SubmissionReceived;
use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorFlowTest extends TestCase
{
    use RefreshDatabase;

    private User $author;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('legacy_files');
        Storage::fake('manuscripts');
        Mail::fake();
        $this->author = User::factory()->create(['role' => 'author']);
    }

    private function payload(array $extra = []): array
    {
        return $extra + [
            'cover_letter' => 'Dear editors', 'title' => 'A title', 'abstract' => 'An abstract', 'keywords' => 'a, b',
            'manuscript' => UploadedFile::fake()->create('my paper.docx', 20),
        ];
    }

    public function test_new_code_is_max_plus_one_and_files_follow_the_naming_scheme(): void
    {
        Manuscript::create(['id' => 10, 'code' => 1797, 'author_id' => $this->author->id]);
        DB::table('settings')->insert(['key' => 'editor_emails', 'value' => 'ed@example.com, ed2@example.com']);

        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload([
            'comment' => 'c',
            'photo' => [1 => UploadedFile::fake()->create('a.jpg', 1), 3 => UploadedFile::fake()->create('b.png', 1)],
            'graph' => [2 => UploadedFile::fake()->create('g.xlsx', 5)],
        ]))->assertRedirect(route('manuscripts.index'));

        $m = Manuscript::where('code', 1798)->firstOrFail();
        $this->assertSame(11, $m->id);
        $this->assertSame('JPOP1798', $m->code_label);
        $this->assertSame($this->author->id, $m->author_id);
        $this->assertSame(0, (int) $m->status);
        $this->assertNotNull($m->submitted_at);

        $paths = $m->files->mapWithKeys(fn ($f) => ["$f->type/$f->slot" => $f->path])->all();
        $this->assertSame([
            'manuscript/1' => 'manuscripts/JPOP1798.docx',
            'photo/1' => 'photographs/JPOP1798_photo1.jpg',
            'photo/3' => 'photographs/JPOP1798_photo3.png',
            'graph/2' => 'graphs/JPOP1798_graph2.xlsx',
        ], $paths);
        foreach ($paths as $p) {
            Storage::disk('manuscripts')->assertExists($p);
        }
        $this->assertTrue($m->files->every(fn ($f) => $f->disk === 'manuscripts'));
        $this->assertDatabaseHas('activity_log', ['content' => 'Submit manuscript']);

        Mail::assertSent(SubmissionReceived::class, fn ($mail) => $mail->hasTo($this->author->email)
            && $mail->hasSubject('Instructions to  author'));
        Mail::assertSent(SubmissionNotice::class, fn ($mail) => $mail->hasTo('ed@example.com') && $mail->hasTo('ed2@example.com'));
    }

    public function test_first_manuscript_in_an_empty_database_gets_code_1(): void
    {
        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload())->assertRedirect();

        $this->assertDatabaseHas('manuscripts', ['id' => 1, 'code' => 1]);
    }

    public function test_required_fields_and_file_types_are_enforced(): void
    {
        $this->actingAs($this->author)->post(route('manuscripts.store'), [])
            ->assertSessionHasErrors(['cover_letter', 'title', 'abstract', 'keywords', 'manuscript']);

        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload([
            'manuscript' => UploadedFile::fake()->create('x.exe', 5),
        ]))->assertSessionHasErrors('manuscript');

        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload([
            'photo' => [11 => UploadedFile::fake()->create('a.jpg', 1)],
        ]))->assertSessionHasErrors('photo');

        $this->assertSame(0, Manuscript::count());
        Mail::assertNothingSent();
    }

    public function test_only_authors_can_submit_or_list(): void
    {
        foreach (['reviewer', 'editor'] as $role) {
            $u = User::factory()->create(['role' => $role]);
            $this->actingAs($u)->get(route('manuscripts.create'))->assertForbidden();
            $this->actingAs($u)->get(route('manuscripts.index'))->assertForbidden();
            $this->actingAs($u)->post(route('manuscripts.store'), $this->payload())->assertForbidden();
        }
        auth()->logout();
        $this->get(route('manuscripts.index'))->assertRedirect(route('login'));
        $this->assertSame(0, Manuscript::count());
    }

    public function test_my_manuscripts_lists_only_own_newest_first_five_per_page(): void
    {
        $other = User::factory()->create(['role' => 'author']);
        for ($i = 1; $i <= 7; $i++) {
            Manuscript::create(['id' => $i, 'code' => $i, 'author_id' => $this->author->id, 'title' => "Mine $i"]);
        }
        Manuscript::create(['id' => 8, 'code' => 8, 'author_id' => $other->id, 'title' => 'Not mine']);

        $r = $this->actingAs($this->author)->get(route('manuscripts.index'))->assertOk();
        $r->assertSeeInOrder(['Mine 7', 'Mine 6', 'Mine 5', 'Mine 4', 'Mine 3']);
        $r->assertDontSee('Mine 2')->assertDontSee('Not mine');
        $this->actingAs($this->author)->get(route('manuscripts.index', ['page' => 2]))->assertSee('Mine 2')->assertSee('Mine 1');
    }

    public function test_my_manuscripts_shows_no_review_data_to_the_author(): void
    {
        $m = Manuscript::create(['id' => 1, 'code' => 1, 'author_id' => $this->author->id, 'title' => 'T']);
        $m->reviews()->create(['slot' => 1, 'reviewer_name' => 'Secret Reviewer', 'recommendation' => 'Rejected']);

        $this->actingAs($this->author)->get(route('manuscripts.index'))
            ->assertOk()->assertDontSee('Secret Reviewer')->assertDontSee('Rejected');
    }

    public function test_corrected_manuscript_is_stored_replaced_and_announced(): void
    {
        $m = Manuscript::create(['id' => 5, 'code' => 77, 'author_id' => $this->author->id, 'title' => 'T']);

        $this->actingAs($this->author)->post(route('manuscripts.corrected', $m), [
            'corrected' => UploadedFile::fake()->create('rev1.docx', 5),
        ])->assertRedirect(route('manuscripts.index'));
        $this->actingAs($this->author)->post(route('manuscripts.corrected', $m), [
            'corrected' => UploadedFile::fake()->create('rev2.pdf', 5),
        ])->assertRedirect();

        $files = ManuscriptFile::where('manuscript_id', 5)->where('type', 'corrected_manuscript')->get();
        $this->assertCount(1, $files);
        $this->assertSame('corrected/JPOP77(corrected).pdf', $files[0]->path);
        Storage::disk('manuscripts')->assertExists('corrected/JPOP77(corrected).pdf');
        Storage::disk('manuscripts')->assertMissing('corrected/JPOP77(corrected).docx');
        $m->refresh();
        $this->assertSame('Corrected manuscript.', $m->corrected_state);
        $this->assertNotNull($m->corrected_at);
        $this->assertDatabaseHas('activity_log', ['content' => 'Upload Corrected File']);
        Mail::assertSent(CorrectedUploaded::class, 2);
    }

    public function test_corrected_figures_use_their_own_types_and_never_overwrite_originals(): void
    {
        $m = Manuscript::create(['id' => 5, 'code' => 77, 'author_id' => $this->author->id]);
        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload([
            'graph' => [4 => UploadedFile::fake()->create('g.xls', 5)],
        ]));
        $new = Manuscript::where('code', 78)->firstOrFail();

        $this->actingAs($this->author)->post(route('manuscripts.figures', $new), [
            'photo' => [1 => UploadedFile::fake()->create('p.jpg', 1)],
            'graph' => [4 => UploadedFile::fake()->create('g2.xlsx', 5)],
        ])->assertRedirect(route('manuscripts.index'));

        $byType = $new->files()->get()->mapWithKeys(fn ($f) => ["$f->type/$f->slot" => $f->path]);
        $this->assertSame('graphs/JPOP78_graph4.xls', $byType['graph/4']);
        $this->assertSame('graphs/JPOP78_graph4(corrected).xlsx', $byType['corrected_graph/4']);
        $this->assertSame('photographs/JPOP78_photo1(corrected).jpg', $byType['corrected_photo/1']);
        Storage::disk('manuscripts')->assertExists('graphs/JPOP78_graph4.xls');
        $this->assertDatabaseHas('activity_log', ['content' => 'Submit corrected figurs']);
    }

    public function test_foreign_manuscripts_cannot_be_corrected_by_others(): void
    {
        $m = Manuscript::create(['id' => 5, 'code' => 77, 'author_id' => $this->author->id]);
        $intruder = User::factory()->create(['role' => 'author']);
        $editor = User::factory()->create(['role' => 'editor']);

        foreach ([$intruder, $editor] as $u) {
            $this->actingAs($u)->get(route('manuscripts.corrected', $m))->assertForbidden();
            $this->actingAs($u)->post(route('manuscripts.corrected', $m), ['corrected' => UploadedFile::fake()->create('a.docx', 1)])->assertForbidden();
            $this->actingAs($u)->get(route('manuscripts.figures', $m))->assertForbidden();
            $this->actingAs($u)->post(route('manuscripts.figures', $m), ['photo' => [1 => UploadedFile::fake()->create('a.jpg', 1)]])->assertForbidden();
        }
        $this->assertSame(0, ManuscriptFile::count());
        Mail::assertNothingSent();
    }

    public function test_uploaded_files_are_served_to_the_author_through_the_policy(): void
    {
        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload())->assertRedirect();
        $f = ManuscriptFile::firstOrFail();

        $this->actingAs($this->author)->get(route('files.show', $f))->assertOk();
        $this->actingAs(User::factory()->create(['role' => 'author']))->get(route('files.show', $f))->assertForbidden();
    }

    public function test_editor_recipients_fall_back_to_editor_accounts(): void
    {
        $e = User::factory()->create(['role' => 'editor', 'email_verified_at' => now()]);
        $this->actingAs($this->author)->post(route('manuscripts.store'), $this->payload())->assertRedirect();

        Mail::assertSent(SubmissionNotice::class, fn ($mail) => $mail->hasTo($e->email));
    }
}
