<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** To the editors when a manuscript was uploaded (waue_mscr_Submit_2.php). */
class SubmissionNotice extends Mailable implements ShouldQueue
{
    public function __construct(public User $author, public Manuscript $manuscript) {}

    public function envelope(): Envelope
    {
        return new Envelope(from: new Address(config('mail.from.address')), subject: 'Manuscript upload and send to system');
    }

    public function content(): Content
    {
        return new Content(view: 'mail.submission-editors');
    }
}
