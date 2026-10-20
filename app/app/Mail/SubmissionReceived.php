<?php

namespace App\Mail;

use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

/** To the author after submitting (webassist/email/waue_mscr_Submit_1.php). */
class SubmissionReceived extends Mailable
{
    public function __construct(public User $author, public Manuscript $manuscript) {}

    public function envelope(): Envelope
    {
        // LEGACY: subject has a double space; sender hard-coded (-> settings in 4.7)
        return new Envelope(from: new Address(config('mail.from.address'), 'journal POP'), subject: 'Instructions to  author');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.submission-author');
    }

    public function attachments(): array
    {
        $path = 'attach/CONSENT_for_AUTHORS.doc';

        return Storage::disk('legacy_files')->exists($path) ? [Attachment::fromStorageDisk('legacy_files', $path)] : [];
    }
}
