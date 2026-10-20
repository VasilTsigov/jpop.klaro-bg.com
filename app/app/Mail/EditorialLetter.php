<?php

namespace App\Mail;

use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

/**
 * A letter sent from the editorial desk (reviewer invitation, reminder, decision, proof, ...).
 * The body is ready HTML (the old pages also mailed whatever the editor typed in a textarea).
 */
class EditorialLetter extends Mailable
{
    /**
     * @param  list<array{0: string, 1: string}>  $files  [disk, path] pairs; missing files are skipped
     */
    public function __construct(
        public string $mailSubject,
        public string $letterHtml,
        public array $files = [],
        public string $fromName = 'Journal POP',
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(from: new Address(config('mail.from.address'), $this->fromName), subject: $this->mailSubject);
    }

    public function content(): Content
    {
        return new Content(view: 'mail.raw');
    }

    public function attachments(): array
    {
        $out = [];
        foreach ($this->files as [$disk, $path]) {
            if ($disk && $path && Storage::disk($disk)->exists($path)) {
                $out[] = Attachment::fromStorageDisk($disk, $path);
            }
        }

        return $out;
    }
}
