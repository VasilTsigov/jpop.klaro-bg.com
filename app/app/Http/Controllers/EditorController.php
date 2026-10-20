<?php

namespace App\Http\Controllers;

use App\Mail\EditorialLetter;
use App\Models\Manuscript;
use App\Models\ManuscriptReview;
use App\Models\ReviewInvitation;
use App\Models\User;
use App\Support\ActivityLog;
use App\Support\EditorialMail;
use App\Support\ManuscriptUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Editorial desk (docs/01-analysis.md steps B, Г, Е, Ж): lists, reviewer assignment, reminders,
 * decision letter, status, direct rejection, proof. Editors only (route middleware + ManuscriptPolicy::editorial).
 */
class EditorController extends Controller
{
    private const LISTS = ['inprogress' => [0, 'All Manuscripts'], 'rejected' => [1, 'Rejected Manuscripts'], 'published' => [2, 'Published Manuscripts']];

    private const LETTER_EXTENSIONS = 'doc,docx,pdf,rtf,odt';

    /** Letters may carry the editor's own markup, but nothing active. */
    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><a><ul><ol><li><div><span><table><tr><td><th>';

    public function index(string $list)
    {
        abort_unless(isset(self::LISTS[$list]), 404);
        [$status, $title] = self::LISTS[$list];

        // LEGACY: INNER JOIN to the author – the 3 orphan manuscripts are not listed (import report)
        $manuscripts = Manuscript::with(['author', 'reviews', 'files'])
            ->has('author')
            ->where('status', $status)
            ->orderByDesc('id')
            ->paginate(15);

        return view('editor.index', compact('manuscripts', 'list', 'title'));
    }

    // --- reviewers ---------------------------------------------------------------------------

    public function assignForm(Manuscript $manuscript, int $slot)
    {
        Gate::authorize('manageReview', $manuscript);
        $reviewers = User::where('role', 'reviewer')->orderBy('last_name')->orderBy('first_name')->get();

        return view('editor.assign', compact('manuscript', 'slot', 'reviewers'));
    }

    public function assign(Request $request, Manuscript $manuscript, int $slot)
    {
        Gate::authorize('manageReview', $manuscript);
        $data = $request->validate([
            'reviewer_id' => ['required', Rule::exists('users', 'id')->where('role', 'reviewer')],
        ]);
        if ((int) $manuscript->status !== 0) {
            return back()->withErrors('Reviewers can be assigned only to manuscripts in progress.');
        }

        $reviewer = User::findOrFail($data['reviewer_id']);
        $now = now();
        $review = DB::transaction(function () use ($manuscript, $slot, $reviewer, $now) {
            // LEGACY: re-assigning resets the confirmation; an earlier review file/recommendation stays
            $review = ManuscriptReview::updateOrCreate(
                ['manuscript_id' => $manuscript->id, 'slot' => $slot],
                [
                    'reviewer_id' => $reviewer->id,
                    'reviewer_name' => trim("$reviewer->first_name $reviewer->last_name"),
                    'invited_at' => $now,
                    'deadline_at' => EditorialMail::dueDate($now),
                    'confirmed_state' => 'pending',
                    'confirmed_at' => null,
                ],
            );
            $this->event($manuscript, 'reviewer_assigned', ['slot' => $slot, 'reviewer_id' => $reviewer->id]);

            return $review;
        });

        ActivityLog::write($request, "Send To Reviewer No $slot");
        $sent = $this->invite($review->load('manuscript'), $reviewer);

        return redirect()->route('editor.list', 'inprogress')->with('status', $sent
            ? "Invitation sent to {$review->reviewer_name}."
            : 'Reviewer saved, but the invitation e-mail could not be sent. Use "resend".');
    }

    /** Fallback when the invitation never arrived (decision 2026-10-20): new links, old ones stop working. */
    public function resend(Request $request, Manuscript $manuscript, int $slot)
    {
        Gate::authorize('manageReview', $manuscript);
        $review = $manuscript->reviews()->with('reviewer')->where('slot', $slot)->firstOrFail();
        abort_unless($review->reviewer && $review->confirmed_state === 'pending' && (int) $manuscript->status === 0, 404);

        ActivityLog::write($request, "Resend invitation to Reviewer No $slot");
        $sent = $this->invite($review, $review->reviewer);

        return back()->with('status', $sent ? 'Invitation sent again.' : 'The invitation e-mail could not be sent.');
    }

    public function reminderForm(Manuscript $manuscript, int $slot)
    {
        Gate::authorize('manageReview', $manuscript);
        $review = $this->assignedReview($manuscript, $slot);
        $text = view('mail.reviewer-reminder', [
            'reviewerName' => "{$review->reviewer->first_name} {$review->reviewer->last_name}",
            'manuscript' => $manuscript,
            'due' => EditorialMail::dueDate($review->invited_at ?? now())->format('d.m.Y'),
        ])->render();

        return view('editor.reminder', compact('manuscript', 'slot', 'review', 'text'));
    }

    public function reminder(Request $request, Manuscript $manuscript, int $slot)
    {
        Gate::authorize('manageReview', $manuscript);
        $review = $this->assignedReview($manuscript, $slot);
        $data = $request->validate(['reminder' => 'required|string|max:65000']);

        EditorialMail::send($review->reviewer->email, new EditorialLetter(
            "Journal POP reminder late review {$manuscript->code_label}",
            strip_tags($data['reminder'], self::ALLOWED_TAGS),
        ));
        $this->event($manuscript, 'reminder_sent', ['slot' => $slot]);
        ActivityLog::write($request, "Reminder to Reviewer No $slot");

        return redirect()->route('editor.list', 'inprogress')->with('status', 'Reminder sent.');
    }

    // --- decision ---------------------------------------------------------------------------------

    public function decisionForm(Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        return view('editor.decision', compact('manuscript'));
    }

    public function decision(Request $request, Manuscript $manuscript, ManuscriptUploads $uploads)
    {
        Gate::authorize('manageReview', $manuscript);
        $request->validate(['letter' => $this->fileRule()]);

        $file = $this->storeUploads($uploads, function () use ($manuscript, $request, $uploads) {
            $f = $uploads->store($manuscript, 'editor_letter', 1, $request->file('letter'));
            $manuscript->update(['editor_letter_file' => $f->original_name, 'editor_letter_at' => now()]);
            $this->event($manuscript, 'decision_letter', ['file' => $f->path]);

            return $f;
        });

        ActivityLog::write($request, "Upload Editor's Decision Comments");
        $author = $manuscript->author;
        EditorialMail::send($author->email, new EditorialLetter(
            'Journal POP - Letter from the editor',
            view('mail.editor-decision', ['authorName' => "$author->first_name $author->last_name"])->render(),
            [[$file->disk, $file->path]],
        ));

        return redirect()->route('editor.list', 'inprogress')->with('status', "Editor's decision letter uploaded and sent to the author.");
    }

    public function rejectForm(Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        return view('editor.reject', compact('manuscript'));
    }

    public function reject(Request $request, Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        $manuscript->update(['status' => 1]);
        $this->event($manuscript, 'rejected_directly');
        ActivityLog::write($request, 'Direct Reject');

        $author = $manuscript->author;
        EditorialMail::send($author->email, new EditorialLetter(
            'Rejected Manuscript',
            view('mail.direct-reject', compact('manuscript'))->render(),
        ));

        return redirect()->route('editor.list', 'inprogress')->with('status', "{$manuscript->code_label} was rejected.");
    }

    // --- status ---------------------------------------------------------------------------------------

    public function statusForm(Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        $manuscript->load('author', 'reviews.reviewer');
        $letters = [];
        foreach ($manuscript->reviews->sortBy('slot') as $review) {
            if ($review->reviewer && $review->recommendation) {
                $letters[$review->slot] = $this->letterToReviewer($manuscript, $review);
            }
        }

        return view('editor.status', compact('manuscript', 'letters'));
    }

    public function status(Request $request, Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        $data = $request->validate(['status' => 'required|in:0,1,2']);
        $manuscript->update(['status' => (int) $data['status']]);
        $this->event($manuscript, 'status_changed', ['status' => (int) $data['status']]);
        ActivityLog::write($request, 'Change Status');

        // LEGACY: waue_mscr_Status_1/2 (Rejected/Published) exist but the old page never sent them
        return back()->with('status', 'Status changed.');
    }

    /** "Letter to Reviewer": the editor may edit each text before it is mailed with the decision letter. */
    public function sendLetters(Request $request, Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        $data = $request->validate(['letter' => 'required|array', 'letter.*' => 'nullable|string|max:65000']);
        $manuscript->load('reviews.reviewer');
        $attach = $manuscript->files()->where('type', 'editor_letter')->first();

        $sent = 0;
        foreach ($manuscript->reviews->sortBy('slot') as $review) {
            $html = $data['letter'][$review->slot] ?? null;
            if (! $review->reviewer || ! $review->recommendation || ! filled($html)) {
                continue;
            }
            EditorialMail::send($review->reviewer->email, new EditorialLetter(
                'Letter to Reviewer',
                strip_tags($html, self::ALLOWED_TAGS),
                $attach ? [[$attach->disk, $attach->path]] : [],
            ));
            $sent++;
        }
        $this->event($manuscript, 'letters_to_reviewers', ['count' => $sent]);
        ActivityLog::write($request, 'Send Letters to Reviewers');

        return back()->with('status', "Letters sent to $sent reviewer(s).");
    }

    // --- proof ---------------------------------------------------------------------------------------------

    public function proofForm(Manuscript $manuscript)
    {
        Gate::authorize('manageReview', $manuscript);
        return view('editor.proof', compact('manuscript'));
    }

    public function proof(Request $request, Manuscript $manuscript, ManuscriptUploads $uploads)
    {
        Gate::authorize('manageReview', $manuscript);
        $request->validate(['proof' => $this->fileRule(), 'proof_letter' => $this->fileRule()]);

        [$proof, $letter] = $this->storeUploads($uploads, function () use ($manuscript, $request, $uploads) {
            $proof = $uploads->store($manuscript, 'proof', 1, $request->file('proof'));
            $letter = $uploads->store($manuscript, 'proof_letter', 1, $request->file('proof_letter'));
            $manuscript->update(['proof_at' => now(), 'proof_editor_at' => now()]);
            $this->event($manuscript, 'proof_uploaded');

            return [$proof, $letter];
        });

        ActivityLog::write($request, 'Upload Proof File');
        $author = $manuscript->author;
        EditorialMail::send($author->email, new EditorialLetter(
            'Journal POP - Proof File',
            view('mail.proof-uploaded', [
                'authorName' => "$author->first_name $author->last_name",
                'proofName' => $proof->original_name, 'letterName' => $letter->original_name,
            ])->render(),
            [[$proof->disk, $proof->path], [$letter->disk, $letter->path]],
        ));

        return redirect()->route('editor.list', 'inprogress')->with('status', 'Proof file uploaded and sent to the author.');
    }

    // --- helpers -----------------------------------------------------------------------------------------------

    /** New one-time agree/decline links (30 days); earlier links are dropped. */
    private function invite(ManuscriptReview $review, User $reviewer): bool
    {
        $manuscript = $review->manuscript;
        $urls = [];
        DB::transaction(function () use ($review, &$urls) {
            $review->invitations()->delete();
            foreach (['agree', 'decline'] as $action) {
                $token = Str::random(48);
                $review->invitations()->create([
                    'action' => $action, 'token_hash' => ReviewInvitation::hash($token),
                    'expires_at' => now()->addDays(30),
                ]);
                $urls[$action] = route('review.respond', $token);
            }
        });

        $files = [];
        if ($review->slot > 1) { // LEGACY: only the invitations of reviewers 2 and 3 carried attachments
            $m = $manuscript->files()->where('type', 'manuscript')->first();
            $files = array_values(array_filter([
                $m ? [$m->disk, $m->path] : null,
                ['legacy_files', 'attach/Checklist.doc'],
                ['legacy_files', 'attach/Instructions_for_reviewer.doc'],
            ]));
        }
        // LEGACY (L9): slot 2 subject lacks a space ("ManuscriptJPOP…"), slot 3 says "for review"
        $subject = match ($review->slot) {
            2 => "Manuscript{$manuscript->code_label} to review",
            3 => "Manuscript {$manuscript->code_label} for review",
            default => "Manuscript {$manuscript->code_label} to review",
        };

        return EditorialMail::send($reviewer->email, new EditorialLetter(
            $subject,
            view('mail.reviewer-invitation', [
                'reviewerName' => $review->reviewer_name, 'manuscript' => $manuscript,
                'agreeUrl' => $urls['agree'], 'declineUrl' => $urls['decline'],
            ])->render(),
            $files,
        ));
    }

    private function letterToReviewer(Manuscript $manuscript, ManuscriptReview $own): string
    {
        $others = $manuscript->reviews->where('slot', '!=', $own->slot)->sortBy('slot')->pluck('recommendation')->all();

        return view('mail.letter-to-reviewer', [
            'reviewerName' => $own->reviewer_name,
            'authorName' => trim("{$manuscript->author->first_name} {$manuscript->author->last_name}"),
            'manuscript' => $manuscript, 'own' => $own->recommendation, 'others' => $others,
        ])->render();
    }

    private function assignedReview(Manuscript $manuscript, int $slot): ManuscriptReview
    {
        $review = $manuscript->reviews()->with('reviewer')->where('slot', $slot)->firstOrFail();
        abort_unless($review->reviewer, 404);

        return $review;
    }

    private function fileRule(): array
    {
        return ['required', 'file', 'max:'.config('jpop.max_upload_kb'), 'extensions:'.self::LETTER_EXTENSIONS];
    }

    /** Runs the storing in a transaction; files are removed again if it fails (see ManuscriptUploads). */
    private function storeUploads(ManuscriptUploads $uploads, callable $work): mixed
    {
        try {
            $result = DB::transaction($work);
        } catch (\Throwable $e) {
            $uploads->rollback();
            throw $e;
        }
        $uploads->commit();

        return $result;
    }

    private function event(Manuscript $manuscript, string $event, array $data = []): void
    {
        DB::table('manuscript_events')->insert([
            'manuscript_id' => $manuscript->id, 'event' => $event, 'actor_id' => auth()->id(),
            'data' => $data ? json_encode($data) : null, 'created_at' => Carbon::now(),
        ]);
    }
}
