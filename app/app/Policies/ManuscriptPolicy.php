<?php

namespace App\Policies;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
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
