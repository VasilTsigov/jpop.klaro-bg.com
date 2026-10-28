<?php

namespace App\Console\Commands;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\ReviewInvitation;
use App\Models\User;
use App\Support\SelftestBrowser;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\UploadedFile;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Runs the whole journal workflow through the application itself (in-process HTTP: real routes, middleware, policies, DB, disks)
 * inside one database transaction that is rolled back at the end, with the mail faked (docs/07-staging-checklist.md).
 * Test users/manuscripts never survive; files written during the run are deleted.
 */
class JpopSelftest extends Command
{
    protected $signature = 'jpop:selftest
        {--send-mail : after the run, really send the captured letters to MAIL_REDIRECT_TO (or --to)}
        {--to= : address for --send-mail (default MAIL_REDIRECT_TO)}
        {--force : allow on APP_ENV=production}';

    protected $description = 'End-to-end self-check of the workflow on this server; everything is rolled back';

    private int $passed = 0;

    private int $failed = 0;

    /** @var list<string> */
    private array $tmp = [];

    private ?Throwable $lastException = null;

    public function handle(): int
    {
        if (app()->environment('production') && ! $this->option('force')) {
            $this->error('Refusing to run on production without --force.');

            return 2;
        }
        // throttle counters (login 10/min, contacts 5/min) live in the cache: use a private one that is reset for every request
        config(['cache.default' => 'array']);
        app('cache')->forgetDriver();
        app()->forgetInstance('cache.store');
        $to = $this->option('to') ?: config('mail.redirect_to');
        if ($this->option('send-mail') && ! $to) {
            $this->error('--send-mail needs MAIL_REDIRECT_TO or --to=address (real letters must never go to real people).');

            return 2;
        }
        if (! $this->environmentChecks()) {
            return 2;
        }

        app(ExceptionHandler::class)->reportable(function (Throwable $e) {
            $this->lastException = $e;
        });

        $real = Mail::getFacadeRoot();
        $fake = Mail::fake();
        $maxManuscript = (int) Manuscript::max('id');
        $files = collect();

        DB::beginTransaction();
        try {
            $this->scenario();

            $files = ManuscriptFile::where('manuscript_id', '>', $maxManuscript)->get(['disk', 'path']);
            $letters = $fake->sent(Mailable::class)->merge($fake->queued(Mailable::class));
            $this->mailReport($letters);

            if ($this->option('send-mail')) {
                Mail::swap($real);
                $this->sendLetters($letters, $to);
            }
        } catch (Throwable $e) {
            $this->failCheck('The run stopped on an unexpected error', get_class($e).': '.$e->getMessage());
        } finally {
            DB::rollBack();
            Mail::swap($real);
            $this->cleanFiles($files);
        }

        $this->newLine();
        $this->line("Passed: {$this->passed}   Failed: {$this->failed}");
        $this->line($this->failed ? '<error> SELFTEST FAILED </error>' : '<info> SELFTEST OK – the database was rolled back, test files removed </info>');

        return $this->failed ? 1 : 0;
    }

    // --- environment ------------------------------------------------------------------------------

    private function environmentChecks(): bool
    {
        $this->line('Environment');
        $ok = true;

        $engines = DB::select("SELECT table_name AS t, engine AS e FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name IN ('users','manuscripts','manuscript_files','manuscript_reviews','activity_log')");
        $bad = collect($engines)->filter(fn ($r) => strtolower($r->e) !== 'innodb')->pluck('t')->all();
        if ($bad) {
            $this->error('  Tables not InnoDB (transactions would not roll back): '.implode(', ', $bad));

            return false;
        }
        $this->check('database is InnoDB (the run can be rolled back)', true);
        $this->check('PHP '.PHP_VERSION.' (8.4 expected)', version_compare(PHP_VERSION, '8.4.0', '>='));
        $this->check('upload_max_filesize '.ini_get('upload_max_filesize').' / post_max_size '.ini_get('post_max_size').' (50M needed)',
            $this->bytes(ini_get('upload_max_filesize')) >= 50 * 1048576 && $this->bytes(ini_get('post_max_size')) >= 50 * 1048576);

        try {
            Storage::disk('manuscripts')->put('selftest.tmp', 'x');
            $w = Storage::disk('manuscripts')->get('selftest.tmp') === 'x';
            Storage::disk('manuscripts')->delete('selftest.tmp');
        } catch (Throwable $e) {
            $w = false;
        }
        $this->check('disk "manuscripts" (new uploads) is writable', $w);
        $this->check('legacy files are readable (attach/CONSENT_for_AUTHORS.doc)', Storage::disk('legacy_files')->exists('attach/CONSENT_for_AUTHORS.doc'));
        $this->check('mailer: '.config('mail.default').', redirect to '.(config('mail.redirect_to') ?: '(none)'),
            config('mail.default') !== 'log' || true);
        $this->check('APP_DEBUG is off', ! config('app.debug'));

        return $ok;
    }

    // --- scenario ---------------------------------------------------------------------------------

    private function scenario(): void
    {
        $tag = 'selftest'.now()->format('His');
        $make = function (string $role, string $name) use ($tag): User {
            $u = new User(['first_name' => 'Selftest', 'last_name' => $name, 'email' => "$tag-$name@selftest.invalid", 'password' => 'Selftest-123']);
            $u->forceFill(['id' => (int) User::max('id') + 1, 'role' => $role, 'email_verified_at' => now(), 'registered_at' => now()])->save();

            return $u;
        };
        $ed = $make('editor', 'Editor');
        $au = $make('author', 'Author');
        $rv = $make('reviewer', 'Reviewer');
        $rv2 = $make('reviewer', 'Reviewer2');
        $pw = 'Selftest-123';

        $this->newLine();
        $this->line('Public pages and anonymous access');
        $anon = new SelftestBrowser;
        foreach (['/', '/users/login', '/users/register', '/instructions', '/contacts', '/issues'] as $p) {
            $this->check("GET $p is 200", $anon->get($p)->getStatusCode() === 200);
        }
        foreach (['/manuscripts', '/manuscripts/submit', '/editor/users', '/editor/logs', '/reviews', '/files/1'] as $p) {
            $r = $anon->get($p);
            $this->check("GET $p without login goes to the login page", $r->isRedirect() && str_contains($r->headers->get('Location'), '/users/login'));
        }
        $this->check('/storage/x is 404', $anon->get('/storage/x')->getStatusCode() === 404);

        $this->newLine();
        $this->line('Registration and sign-in');
        $reg = new SelftestBrowser;
        $reg->get('/users/register');
        $email = "$tag-new@selftest.invalid";
        $r = $reg->post('/users/register', ['email' => $email, 'password' => $pw, 'password_confirmation' => $pw, 'first_name' => 'New', 'last_name' => 'Person',
            'address' => 'Street 1', 'city' => 'Sofia', 'country' => 'Bulgaria']);
        $this->check('registration is accepted', $r->isRedirect() && str_contains((string) $r->headers->get('Location'), 'thank-you'), $this->why($r));
        $new = User::where('email', $email)->first();
        $this->check('new account is an unconfirmed author', $new && $new->role === 'author' && ! $new->email_verified_at);
        $reg->get('/users/login');
        $reg->post('/users/login', ['email' => $email, 'password' => $pw]);
        $this->check('login before confirmation is refused', $reg->get('/users/welcome')->isRedirect());
        $this->check('confirmation link works', $new && $reg->get('/users/confirm?UID='.$new->id.'&code='.$new->verification_code)->getStatusCode() === 200);
        $reg->get('/users/login');
        $reg->post('/users/login', ['email' => $email, 'password' => $pw]);
        $this->check('login after confirmation works', $reg->get('/users/welcome')->getStatusCode() === 200);

        $wrong = new SelftestBrowser;
        $wrong->get('/users/login');
        $wrong->post('/users/login', ['email' => $au->email, 'password' => 'nope']);
        $this->check('wrong password does not sign in', $wrong->get('/users/welcome')->isRedirect());

        $a = $this->login($au->email, $pw);
        $this->check('author signs in and sees "Submit Manuscripts"', str_contains($a->get('/users/welcome')->getContent(), 'Submit Manuscripts'));
        $this->check('author gets 403 on /editor/users', $a->get('/editor/users')->getStatusCode() === 403);

        $this->newLine();
        $this->line('Author: submit');
        $countBefore = Manuscript::count();
        $a->get('/manuscripts/submit');
        $r = $a->post('/manuscripts/submit', ['cover_letter' => 'Dear editors', 'title' => "Selftest $tag", 'abstract' => 'Abstract text', 'keywords' => 'a, b',
            'manuscript' => $this->upload('paper.exe')]);
        $this->check('a .exe manuscript is refused', Manuscript::count() === $countBefore);
        $r = $a->post('/manuscripts/submit', ['cover_letter' => 'Dear editors', 'title' => "Selftest $tag", 'abstract' => 'Abstract text', 'keywords' => 'a, b', 'comment' => 'c',
            'manuscript' => $this->upload('my paper.docx'), 'photo' => [1 => $this->upload('a.jpg')], 'graph' => [1 => $this->upload('g.xlsx')]]);
        $m = Manuscript::where('author_id', $au->id)->latest('id')->first();
        $this->check('manuscript is submitted', $r->isRedirect() && $m !== null, $this->why($r));
        if (! $m) {
            return;
        }
        $this->check('code is MAX+1 and the label is JPOP<code>', $m->code_label === 'JPOP'.$m->code && $m->id === $maxId = (int) Manuscript::max('id'));
        $files = $m->files()->get();
        $this->check('manuscript, photo and graph files are on disk', $files->count() === 3 && $files->every(fn ($f) => Storage::disk($f->disk)->exists($f->path)));
        $this->check('"My manuscripts" lists it', str_contains($a->get('/manuscripts')->getContent(), "Selftest $tag"));
        $mf = $files->firstWhere('type', 'manuscript');
        $this->check('author downloads the own manuscript (/files/{id})', $a->get("/files/{$mf->id}")->getStatusCode() === 200);
        $this->check('author opens the own abstract', $a->get("/manuscripts/{$m->id}/abstract")->getStatusCode() === 200);

        $this->check('another user cannot download it', $this->login($rv2->email, $pw)->get("/files/{$mf->id}")->getStatusCode() === 403);
        $this->check('another user cannot read the abstract', $this->login($rv2->email, $pw)->get("/manuscripts/{$m->id}/abstract")->getStatusCode() === 403);

        $this->newLine();
        $this->line('Editor: assign reviewer, agree link');
        $e = $this->login($ed->email, $pw);
        $this->check('editor sees the manuscript in the "in progress" list', str_contains($e->get('/editor/lists/inprogress')->getContent(), "Selftest $tag"));
        $this->check('reviewer menu lists the reviewer', str_contains($e->get("/editor/manuscripts/{$m->id}/reviewers/1")->getContent(), $rv->email));
        $r = $e->post("/editor/manuscripts/{$m->id}/reviewers/1", ['reviewer_id' => $rv->id]);
        $this->check('reviewer is assigned', $r->isRedirect() && $m->reviews()->where('slot', 1)->where('reviewer_id', $rv->id)->exists(), $this->why($r));
        $tokens = $this->invitationTokens($rv->email);
        $this->check('the invitation mail has agree and decline links', count($tokens) === 2 && ReviewInvitation::where('token_hash', ReviewInvitation::hash($tokens[0] ?? ''))->exists());
        if (count($tokens) === 2) {
            $link = new SelftestBrowser;
            $page = $link->get('/review/'.$tokens[0]);
            $this->check('agree page opens without login and changes nothing', $page->getStatusCode() === 200 && $m->reviews()->where('slot', 1)->value('confirmed_state') === 'pending');
            $r = $link->post('/review/'.$tokens[0], []);
            $this->check('agree confirms the review', $r->getStatusCode() === 200 && $m->reviews()->where('slot', 1)->value('confirmed_state') === 'confirmed');
            $this->check('the used link no longer works', str_contains($link->get('/review/'.$tokens[0])->getContent(), 'no longer valid'));
        }
        $this->check('editor cannot assign the manuscript to its author', ! str_contains($e->get("/editor/manuscripts/{$m->id}/reviewers/2")->getContent(), $au->email));

        $this->newLine();
        $this->line('Reviewer: review upload');
        $v = $this->login($rv->email, $pw);
        $this->check('"Manuscripts for review" lists the manuscript', str_contains($v->get('/reviews')->getContent(), 'JPOP'.$m->code));
        $review = $m->reviews()->where('slot', 1)->first();
        $this->check('reviewer downloads the manuscript to review', $v->get("/files/{$mf->id}")->getStatusCode() === 200);
        $this->check('another reviewer is refused', $this->login($rv2->email, $pw)->get("/reviews/{$review->id}/upload")->getStatusCode() === 403);
        $v->get("/reviews/{$review->id}/upload");
        $r = $v->post("/reviews/{$review->id}/upload", ['review_file' => $this->upload('review.docx'), 'checklist_file' => $this->upload('check.doc'), 'recommendation' => 'Minor revision']);
        $this->check('review is uploaded with recommendation', $r->isRedirect() && $review->fresh()->recommendation === 'Minor revision' && $review->fresh()->reviewed_at, $this->why($r));
        $rf = $m->files()->where('type', 'review')->first();
        $this->check('the author cannot download the review', $rf && $a->get("/files/{$rf->id}")->getStatusCode() === 403);
        $this->check('reviewer downloads own review', $rf && $v->get("/files/{$rf->id}")->getStatusCode() === 200);

        $this->newLine();
        $this->line('Editor: reminder, decision, status, proof; author: corrected');
        $this->check('reminder form opens', $e->get("/editor/manuscripts/{$m->id}/reminder/1")->getStatusCode() === 200);
        $r = $e->post("/editor/manuscripts/{$m->id}/reminder/1", ['reminder' => '<p>Please hurry</p>']);
        $this->check('reminder is sent', $r->isRedirect(), $this->why($r));
        $e->get("/editor/manuscripts/{$m->id}/decision");
        $r = $e->post("/editor/manuscripts/{$m->id}/decision", ['letter' => $this->upload('decision.docx')]);
        $this->check('editor\'s decision is uploaded', $r->isRedirect() && $m->files()->where('type', 'editor_letter')->exists(), $this->why($r));
        $df = $m->files()->where('type', 'editor_letter')->first();
        $this->check('the author downloads the decision letter', $df && $a->get("/files/{$df->id}")->getStatusCode() === 200);
        $r = $e->post("/editor/manuscripts/{$m->id}/status/letters", []);
        $this->check('"Letters to reviewers" does not crash', $r->getStatusCode() < 500, $this->why($r));

        $a->get("/manuscripts/{$m->id}/corrected");
        $r = $a->post("/manuscripts/{$m->id}/corrected", ['corrected' => $this->upload('corrected.docx')]);
        $this->check('author uploads the corrected manuscript', $r->isRedirect() && $m->files()->where('type', 'corrected_manuscript')->exists(), $this->why($r));
        $other = $this->login($rv2->email, $pw);
        $other->get('/users/welcome');
        $this->check('another user cannot upload a corrected manuscript', $other->post("/manuscripts/{$m->id}/corrected", ['corrected' => $this->upload('x.docx')])->getStatusCode() === 403);

        $e->get("/editor/manuscripts/{$m->id}/proof");
        $r = $e->post("/editor/manuscripts/{$m->id}/proof", ['proof' => $this->upload('proof.pdf'), 'proof_letter' => $this->upload('letter.docx')]);
        $this->check('proof is uploaded and mailed', $r->isRedirect() && $m->files()->where('type', 'proof')->exists(), $this->why($r));
        $pf = $m->files()->where('type', 'proof')->first();
        $this->check('the author downloads the proof', $pf && $a->get("/files/{$pf->id}")->getStatusCode() === 200);

        $e->post("/editor/manuscripts/{$m->id}/status", ['status' => 2]);
        $this->check('status → published, shows in the Published list', (int) $m->fresh()->status === 2 && str_contains($e->get('/editor/lists/published')->getContent(), "Selftest $tag"));
        $e->post("/editor/manuscripts/{$m->id}/status", ['status' => 0]);
        $r = $e->post("/editor/manuscripts/{$m->id}/reject", []);
        $this->check('direct reject → rejected', $r->isRedirect() && (int) $m->fresh()->status === 1, $this->why($r));

        $this->newLine();
        $this->line('Every role submits; editor pages; contact form');
        $r2 = $this->login($rv2->email, $pw);
        $this->check('reviewer sees "Submit Manuscripts"', str_contains($r2->get('/users/welcome')->getContent(), 'Submit Manuscripts'));
        $r2->get('/manuscripts/submit');
        $r = $r2->post('/manuscripts/submit', ['cover_letter' => 'c', 'title' => "Selftest rev $tag", 'abstract' => 'a', 'keywords' => 'k', 'manuscript' => $this->upload('r.docx')]);
        $this->check('a reviewer submits an own manuscript', $r->isRedirect() && Manuscript::where('author_id', $rv2->id)->exists(), $this->why($r));
        $this->check('editor opens Users', $e->get('/editor/users')->getStatusCode() === 200);
        $this->check('editor opens Logs', $e->get('/editor/logs')->getStatusCode() === 200);
        $this->check('editor finds the manuscript with Search', str_contains($e->get('/editor/search?q='.urlencode("Selftest $tag"))->getContent(), "Selftest $tag"));
        $this->check('author gets 403 on Search and Logs', $a->get('/editor/search')->getStatusCode() === 403 && $a->get('/editor/logs')->getStatusCode() === 403);

        $c = new SelftestBrowser;
        $c->get('/contacts');
        $r = $c->post('/contacts', ['name' => 'Selftest', 'email' => $au->email, 'message' => 'Hello from the selftest']);
        $this->check('contact form is accepted', $r->isRedirect() && str_contains((string) $r->headers->get('Location'), 'thank-you'), $this->why($r));

        $r = $a->post('/users/logout', []);
        $this->check('logout works', $r->isRedirect() && $a->get('/manuscripts')->isRedirect());
    }

    // --- letters ----------------------------------------------------------------------------------

    private function invitationTokens(string $reviewerEmail): array
    {
        $letter = Mail::queued(\App\Mail\EditorialLetter::class, fn ($l) => $l->hasTo($reviewerEmail))->first();
        if ($letter && preg_match_all('#/review/([A-Za-z0-9]{48})#', $letter->letterHtml, $t)) {
            return $t[1];
        }

        return [];
    }

    private function mailReport($letters): void
    {
        $this->newLine();
        $this->line('Letters produced by the run (faked, nothing was sent)');
        $rows = $letters->map(function (Mailable $m) {
            $subject = $m->mailSubject ?? $m->envelope()->subject ?? class_basename($m);
            $to = collect($m->to)->pluck('address')->implode(', ');
            $att = method_exists($m, 'attachments') ? count($m->attachments()) : 0;

            return [class_basename($m), $subject, $to, $att];
        })->all();
        $this->table(['Mailable', 'Subject', 'To', 'Attachments'], $rows);
        $this->check('at least one letter per main step (>= 12 letters)', count($rows) >= 12);
    }

    private function sendLetters($letters, string $to): void
    {
        $this->newLine();
        $this->line("Sending the captured letters to $to (links in them point to rolled-back data and will not work)");
        Mail::alwaysTo($to);
        $n = 0;
        foreach ($letters as $m) {
            try {
                $subject = $m->mailSubject ?? $m->envelope()->subject ?? class_basename($m);
                $m->subject('[selftest] '.$subject);
                Mail::mailer()->sendNow($m);
                $n++;
            } catch (Throwable $e) {
                $this->failCheck('Could not send a letter', $e->getMessage());
            }
        }
        $this->check("$n letters sent to $to", $n === $letters->count());
    }

    // --- helpers ----------------------------------------------------------------------------------

    private function login(string $email, string $password): SelftestBrowser
    {
        $b = new SelftestBrowser;
        $b->get('/users/login');
        $b->post('/users/login', ['email' => $email, 'password' => $password]);

        return $b;
    }

    private function upload(string $name): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'jpopst');
        file_put_contents($path, "selftest $name");
        $this->tmp[] = $path;

        return new UploadedFile($path, $name, null, null, true);
    }

    private function why($response): string
    {
        $code = $response->getStatusCode();
        if ($code >= 500 && $this->lastException) {
            return "HTTP $code: ".get_class($this->lastException).': '.$this->lastException->getMessage();
        }

        return $code === 302 ? '' : "HTTP $code";
    }

    private function cleanFiles($files): void
    {
        $n = 0;
        foreach ($files as $f) {
            if ($f->disk && $f->disk !== 'legacy_files' && Storage::disk($f->disk)->exists($f->path)) {
                Storage::disk($f->disk)->delete($f->path);
                $n++;
            }
        }
        foreach ($this->tmp as $p) {
            @unlink($p);
        }
        $this->line("Test files removed: $n");
    }

    private function bytes(string $v): int
    {
        $n = (int) $v;

        return match (strtolower(substr(trim($v), -1))) {
            'g' => $n * 1073741824, 'm' => $n * 1048576, 'k' => $n * 1024, default => $n,
        };
    }

    private function check(string $label, bool $ok, string $detail = ''): void
    {
        $ok ? $this->passed++ : $this->failed++;
        $this->line(($ok ? '  <info>✓</info> ' : '  <error>✗</error> ').$label.($ok || $detail === '' ? '' : "  — $detail"));
    }

    private function failCheck(string $label, string $detail): void
    {
        $this->check($label, false, $detail);
    }
}
