<?php

namespace App\Http\Controllers;

use App\Mail\EditorialLetter;
use App\Models\Manuscript;
use App\Support\EditorialMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

/**
 * Helper pages of the old site (docs/03-plan.md 4.8): instructions, issues, contacts.
 * LEGACY: sendemails/ (mass invitation letters to the `emailstest` list from a second database) is not migrated – see STATUS.md.
 */
class PageController extends Controller
{
    public function instructions()
    {
        return view('pages.instructions');
    }

    /** Consent form for authors; public because authors need it before they can log in (also attached to the submission e-mail). */
    public function consent()
    {
        $path = 'attach/CONSENT_for_AUTHORS.doc';
        abort_unless(Storage::disk('legacy_files')->exists($path), 404);

        return Storage::disk('legacy_files')->download($path);
    }

    /** LEGACY: issues_Index.php is an empty page with a title only. */
    public function issues()
    {
        return view('pages.issues');
    }

    public function contactForm()
    {
        return view('pages.contacts');
    }

    /** LEGACY: the CAPTCHA is replaced by a throttle and a honeypot field (same decision as registration, 4.2). */
    public function contactSend(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:150'],
            'name' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:5000'],
            'website' => ['nullable', 'max:0'],
        ], [
            'email.required' => 'Please enter email.', 'email.email' => 'Please enter valid email.',
            'name.required' => 'Please enter your name.', 'message.required' => 'Please enter your message.',
        ]);

        EditorialMail::send([], new EditorialLetter(
            'Message from contact form',
            view('mail.contact', $data)->render(),
        ));

        return redirect()->route('contacts.thanks');
    }

    /** Abstract / cover letter of one manuscript (ManuscriptPolicy::viewText closes the "any id" hole of the old pages). */
    public function text(Manuscript $manuscript, string $what)
    {
        Gate::authorize('viewText', $manuscript);
        abort_unless($manuscript->author, 404); // LEGACY: the old query joined users

        return view('pages.text', [
            'm' => $manuscript,
            'heading' => $what === 'abstract' ? 'Abstract' : 'Cover Letter',
            'body' => $what === 'abstract' ? $manuscript->abstract : $manuscript->cover_letter,
        ]);
    }
}
