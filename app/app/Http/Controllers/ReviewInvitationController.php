<?php

namespace App\Http\Controllers;

use App\Mail\EditorialLetter;
use App\Models\ReviewInvitation;
use App\Support\EditorialMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Agree / decline link from the invitation e-mail (docs/01-analysis.md step B). Works without login,
 * like the old mscrNConfirmYes/No pages, but the link is a one-time token that expires after 30 days.
 * GET only shows the page; the click that changes anything is a POST, so mail scanners cannot use it up.
 */
class ReviewInvitationController extends Controller
{
    public function show(string $token)
    {
        $invitation = $this->find($token);

        return view('review.respond', ['invitation' => $invitation, 'manuscript' => $invitation?->review->manuscript]);
    }

    public function respond(Request $request, string $token)
    {
        $invitation = $this->find($token);
        abort_unless($invitation, 404);
        $info = (string) ($request->validate(['info' => 'nullable|string|max:5000'])['info'] ?? '');
        $review = $invitation->review;
        $manuscript = $review->manuscript;
        $name = $review->reviewer_name;

        DB::transaction(function () use ($invitation, $review, $manuscript) {
            if ($invitation->action === 'agree') {
                $review->update(['confirmed_state' => 'confirmed', 'confirmed_at' => now()]);
            } else {
                // LEGACY: declining empties the slot so the editor can pick someone else
                DB::table('manuscript_events')->insert([
                    'manuscript_id' => $manuscript->id, 'event' => 'reviewer_declined', 'actor_id' => null,
                    'data' => json_encode(['slot' => $review->slot, 'reviewer_id' => $review->reviewer_id]),
                    'created_at' => now(),
                ]);
                $review->update(['confirmed_state' => 'declined', 'confirmed_at' => now(), 'reviewer_id' => null, 'reviewer_name' => null]);
            }
            $review->invitations()->delete(); // the links are used up
        });

        if ($invitation->action === 'decline') {
            $text = trim($info) !== '' ? $info : "Reviewer $name declined to review manuscript {$manuscript->code_label}.";
            EditorialMail::send([], new EditorialLetter('Message from Reviewer', view('mail.reviewer-message', ['info' => $text])->render()));
        }

        return view('review.done', ['action' => $invitation->action, 'manuscript' => $manuscript]);
    }

    private function find(string $token): ?ReviewInvitation
    {
        return ReviewInvitation::with('review.manuscript')
            ->where('token_hash', ReviewInvitation::hash($token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }
}
