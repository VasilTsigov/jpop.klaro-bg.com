<?php

namespace Tests\Feature;

use App\Mail\ConfirmRegistration;
use App\Mail\EditorialLetter;
use App\Mail\SubmissionNotice;
use App\Mail\SubmissionReceived;
use App\Models\User;
use App\Support\EditorialMail;
use App\Support\EditorRecipients;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Exceptions;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MailTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_mailables_are_queued(): void
    {
        foreach ([ConfirmRegistration::class, EditorialLetter::class, SubmissionNotice::class, SubmissionReceived::class] as $class) {
            $this->assertTrue(is_subclass_of($class, ShouldQueue::class), $class);
        }
    }

    public function test_editor_emails_command_sets_recipients_and_validates(): void
    {
        $this->artisan('jpop:setting', ['key' => 'editor_emails', 'value' => 'a@example.com, b@example.com'])->assertSuccessful();
        $this->assertSame(['a@example.com', 'b@example.com'], EditorRecipients::emails());

        $this->artisan('jpop:setting', ['key' => 'editor_emails', 'value' => 'not-an-email'])->assertFailed();
        $this->assertSame(['a@example.com', 'b@example.com'], EditorRecipients::emails());

        $this->artisan('jpop:setting', ['key' => 'editor_emails', 'value' => '-'])->assertSuccessful();
        $editor = User::factory()->create(['role' => 'editor']);
        $this->assertSame([$editor->email], EditorRecipients::emails());
    }

    public function test_mail_failure_is_swallowed(): void
    {
        Exceptions::fake();
        Mail::shouldReceive('to')->andThrow(new \RuntimeException('smtp down'));
        $this->assertFalse(EditorialMail::deliver('x@example.com', new EditorialLetter('s', '<p>b</p>')));
        Exceptions::assertReported(\RuntimeException::class);
    }
}
