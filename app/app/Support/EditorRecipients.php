<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class EditorRecipients
{
    /** @return list<string> */
    public static function emails(): array
    {
        $list = DB::table('settings')->where('key', 'editor_emails')->value('value')
            ?: config('jpop.editor_emails');
        $emails = array_values(array_filter(array_map('trim', explode(',', (string) $list))));
        if ($emails) {
            return $emails;
        }

        return User::where('role', 'editor')->whereNotNull('email_verified_at')->pluck('email')->all();
    }
}
