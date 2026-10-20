<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

class ConfirmRegistration extends Mailable implements ShouldQueue
{
    public function __construct(public User $user) {}

    public function envelope(): Envelope
    {
        // LEGACY: sender hard-coded in webassist/email/waue_users_Register_2.php (-> settings in 4.7)
        return new Envelope(
            from: new Address(config('mail.from.address'), 'Journal POP'),
            subject: 'Please confirm registration',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.confirm', with: [
            'url' => route('confirm', ['UID' => $this->user->id, 'code' => $this->user->verification_code]),
        ]);
    }
}
