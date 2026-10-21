<?php

namespace App\Support;

use App\Mail\EditorialLetter;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;

class EditorialMail
{
    /**
     * Sends the letter to every address separately (as the old pages did). LEGACY: the old pages had
     * the editor's own addresses hard-coded as extra recipients; they are now EditorRecipients (-> 4.7).
     * A mail failure must not undo a recorded action, so it is reported and swallowed.
     *
     * @param  list<string>|string  $to
     */
    public static function send(array|string $to, EditorialLetter $letter, bool $copyEditors = true): bool
    {
        $addresses = array_merge((array) $to, $copyEditors ? EditorRecipients::emails() : []);
        $seen = [];
        $ok = true;
        foreach ($addresses as $address) {
            $address = trim((string) $address);
            if ($address === '' || isset($seen[strtolower($address)])) {
                continue;
            }
            $seen[strtolower($address)] = true;
            try {
                Mail::to($address)->send(clone $letter);
                ActivityLog::event('mail', "Mail to $address: {$letter->mailSubject}", 'info', ['to' => $address, 'subject' => $letter->mailSubject]);
            } catch (\Throwable $e) {
                report($e);
                $ok = false;
            }
        }

        return $ok;
    }

    /** Any other mailable: queued; a mail failure must not lose a recorded action. */
    public static function deliver(array|string $to, Mailable $mailable): bool
    {
        try {
            Mail::to($to)->send($mailable);
            $subject = $mailable->envelope()->subject ?? class_basename($mailable);
            ActivityLog::event('mail', 'Mail to '.implode(', ', (array) $to).": $subject", 'info', ['to' => (array) $to, 'subject' => $subject]);

            return true;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    /** "dd.mm.yyyy" + 1 month with PHP overflow (31.01 -> 03.03). LEGACY: splitDate() in myfunc.php. */
    public static function dueDate(\DateTimeInterface $invitedAt): \Carbon\CarbonInterface
    {
        return \Illuminate\Support\Carbon::instance($invitedAt)->startOfDay()->addMonthWithOverflow();
    }
}
