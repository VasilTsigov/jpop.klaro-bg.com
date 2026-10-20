<?php

namespace App\Support;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActivityLog
{
    /** Same activity_log rows the old pages wrote (logID is not auto-increment in the new schema). */
    public static function write(Request $request, string $content): void
    {
        $u = $request->user();
        DB::table('activity_log')->insert([
            'id' => (int) DB::table('activity_log')->max('id') + 1,
            'user' => "{$u->first_name} {$u->last_name}, {$u->email}",
            'ip' => (string) $request->ip(),
            'content' => $content,
            'logged_at' => now(),
        ]);
    }
}
