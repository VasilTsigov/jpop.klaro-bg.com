<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the editors when the author uploaded the corrected manuscript (waue_mscr_UpldFileCorrected_1.php). */
class CorrectedUploaded extends Mailable implements ShouldQueue
{
    public function __construct(public User $author, public Manuscript $manuscript) {}

    public function envelope(): Envelope
    {
        // LEGACY: "Autor" typo kept in the subject
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Journal POP'),
            subject: "Autor {$this->author->first_name} {$this->author->last_name} has uploaded his corrected manuscript",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.corrected-uploaded');
    }
}
