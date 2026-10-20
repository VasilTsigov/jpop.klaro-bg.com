<?php

namespace App\Http\Controllers;

use App\Mail\EditorialLetter;
use App\Models\ManuscriptReview;
use App\Support\ActivityLog;
use App\Support\EditorialMail;
use App\Support\ManuscriptUploads;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Reviewer side (mscr_Reviewer.php, mscr_UpldFileReviewer_N.php): own assignments, upload of the reviewed
 * manuscript + checklist + recommendation. The three copies of the old page are one here (L7).
 */
class ReviewerController extends Controller
{
    private const RECOMMENDATIONS = ['Minor revision', 'Major revision', 'Rejected'];

    public function index(Request $request)
    {
        abort_unless($request->user()->role === 'reviewer', 403);
        // LEGACY: every slot carrying the reviewer's id, newest manuscript first, 5 per page
        $reviews = ManuscriptReview::with('manuscript.files')
            ->where('reviewer_id', $request->user()->id)
            ->where('confirmed_state', '!=', 'declined')
            ->orderByDesc('manuscript_id')
            ->paginate(5);

        return view('reviews.index', ['reviews' => $reviews]);
    }

    public function create(ManuscriptReview $review)
    {
        Gate::authorize('uploadReview', $review);

        return view('reviews.upload', ['review' => $review, 'manuscript' => $review->manuscript, 'recommendations' => self::RECOMMENDATIONS]);
    }

    public function store(Request $request, ManuscriptReview $review, ManuscriptUploads $uploads)
    {
        Gate::authorize('uploadReview', $review);
        $rules = ['file', 'max:'.config('jpop.max_upload_kb'), 'extensions:doc,docx,pdf,rtf,odt,zip,rar'];
        $data = $request->validate([
            'review_file' => ['required', ...$rules],
            'checklist_file' => ['required', ...$rules],
            'recommendation' => ['required', 'in:'.implode(',', self::RECOMMENDATIONS)],
        ], [
            'review_file.required' => 'Please select you reviewed file to upload.',
            'checklist_file.required' => 'Please select checklist file.',
            'recommendation.required' => 'Please select recommendation.',
        ]);
        $manuscript = $review->manuscript;

        try {
            DB::transaction(function () use ($review, $manuscript, $request, $uploads, $data) {
                $file = $uploads->store($manuscript, 'review', $review->slot, $request->file('review_file'));
                $checklist = $uploads->store($manuscript, 'checklist', $review->slot, $request->file('checklist_file'));
                $review->update([
                    'review_file_id' => $file->id, 'checklist_file_id' => $checklist->id,
                    'recommendation' => $data['recommendation'], 'reviewed_at' => now(),
                ]);
                DB::table('manuscript_events')->insert([
                    'manuscript_id' => $manuscript->id, 'event' => 'review_uploaded', 'actor_id' => $request->user()->id,
                    'data' => json_encode(['slot' => $review->slot, 'recommendation' => $data['recommendation']]),
                    'created_at' => now(),
                ]);
            });
        } catch (\Throwable $e) {
            $uploads->rollback();
            throw $e;
        }
        $uploads->commit();

        ActivityLog::write($request, "Reviewer {$review->slot} uploads file");
        $this->notify($review->refresh());

        return redirect()->route('reviews.index')->with('status', 'Thank you! Your review has been uploaded.');
    }

    /** Checklist and instructions for reviewers (static helper files imported from attach/). */
    public function help(Request $request, string $name)
    {
        abort_unless(in_array($request->user()->role, ['reviewer', 'editor'], true), 403);
        $path = $name === 'checklist' ? 'attach/Checklist.doc' : 'attach/Instructions_for_reviewer.doc';
        abort_unless(Storage::disk('legacy_files')->exists($path), 404);

        return Storage::disk('legacy_files')->download($path);
    }

    private function notify(ManuscriptReview $review): void
    {
        $m = $review->manuscript;
        $author = $m->author;
        $name = $review->reviewer_name;
        $authorName = trim("{$author->first_name} {$author->last_name}");

        // LEGACY: author notice. The old mail also attached the reviewer's checklist; dropped because authors
        // do not see reviews (decision 2026-10-20) -> deviation from 1:1, noted in STATUS.md.
        EditorialMail::send($author->email, new EditorialLetter(
            'Journal POP - Reviewed manuscript  ',
            view('mail.review-author', ['authorName' => $authorName, 'm' => $m, 'slot' => $review->slot])->render(),
        ), copyEditors: false);

        EditorialMail::send([], new EditorialLetter(
            "Reviewer $name uploads file",
            view('mail.review-editors', ['name' => $name, 'm' => $m, 'authorName' => $authorName])->render(),
        ));
    }
}
