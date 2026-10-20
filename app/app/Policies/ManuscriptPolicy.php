<?php

namespace App\Policies;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\ManuscriptReview;
use App\Models\User;

/**
 * Closes L1/L2 (docs/03-plan.md §4). Stricter than the old site, which served every
 * file to anyone who knew mnscrpt_id.
 */
class ManuscriptPolicy
{
    /** Material the author submitted (and later corrected). */
    private const SUBMISSION = ['manuscript', 'photo', 'graph', 'corrected_manuscript', 'corrected_photo', 'corrected_graph'];

    /** Editor's letters and proofs are exchanged between editor and author only. */
    private const AUTHOR_SIDE = ['editor_letter', 'proof', 'proof_letter'];

    /**
     * Editorial desk: lists, reviewers, reminders, decision, status, proof. Editors only
     * (the old mscr_Status / mscr_DirectReject pages had no guard at all – see docs/02-characterization.md).
     */
    public function editorial(User $user): bool
    {
        return $user->role === 'editor';
    }

    /** Submit a new manuscript / list one's own: authors, and editors/admins as authors (decision 2026-10-20). */
    public function submit(User $user): bool
    {
        return in_array($user->role, ['author', 'editor'], true);
    }

    /**
     * Editorial handling of a manuscript (reviewers, reminders, decision, reject, status, letters, proof): an editor may not do it for their own
     * manuscript – another editor must (conflict of interest, decision 2026-10-20).
     */
    public function manageReview(User $user, Manuscript $manuscript): bool
    {
        return $user->role === 'editor' && $manuscript->author_id !== $user->id;
    }

    /** Corrected manuscript and corrected figures: only the manuscript's own author. */
    public function correct(User $user, Manuscript $manuscript): bool
    {
        return $this->submit($user) && $manuscript->author_id === $user->id;
    }

    /** Reviewer's own upload page: only the slot assigned to them (a declined slot has no reviewer). */
    public function uploadReview(User $user, ManuscriptReview $review): bool
    {
        return $user->role === 'reviewer' && $review->reviewer_id === $user->id && $review->confirmed_state !== 'declined';
    }

    /**
     * Abstract and cover letter pages. The old pages showed them to any logged-in user for any id;
     * now: editor, the manuscript's author, or a reviewer assigned to it (not declined).
     */
    public function viewText(User $user, Manuscript $manuscript): bool
    {
        return match ($user->role) {
            'editor' => true,
            'author' => $manuscript->author_id === $user->id,
            'reviewer' => $manuscript->reviews()->where('reviewer_id', $user->id)->where('confirmed_state', '!=', 'declined')->exists(),
            default => false,
        };
    }

    public function downloadFile(User $user, Manuscript $manuscript, ManuscriptFile $file): bool
    {
        if ($file->manuscript_id !== $manuscript->id) {
            return false;
        }

        return match ($user->role) {
            'editor' => true,
            // reviews/checklists are not shown to the author (decision 2026-10-20)
            'author' => $manuscript->author_id === $user->id
                && in_array($file->type, [...self::SUBMISSION, ...self::AUTHOR_SIDE], true),
            'reviewer' => $this->reviewerMayDownload($user, $manuscript, $file),
            default => false,
        };
    }

    private function reviewerMayDownload(User $user, Manuscript $manuscript, ManuscriptFile $file): bool
    {
        $slots = $manuscript->reviews()
            ->where('reviewer_id', $user->id)
            ->where('confirmed_state', '!=', 'declined')
            ->pluck('slot');
        if ($slots->isEmpty()) {
            return false;
        }
        if (in_array($file->type, self::SUBMISSION, true)) {
            return true;
        }

        // only the reviewer's own review and checklist, never other reviewers' ones
        return in_array($file->type, ['review', 'checklist'], true) && $slots->contains($file->slot);
    }
}
