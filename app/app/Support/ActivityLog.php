<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * The editors' activity journal (docs/03-plan.md §9). Logging must never break the action it records,
 * so every failure here is swallowed. Never put passwords, tokens or letter bodies in `$context`.
 */
class ActivityLog
{
    public const CATEGORIES = ['auth' => 'Sign-in', 'view' => 'Views', 'upload' => 'Uploads', 'editorial' => 'Editorial',
        'mail' => 'Mail', 'security' => 'Security', 'error' => 'Errors', 'system' => 'System', 'legacy' => 'Legacy'];

    public const LEVELS = ['info', 'warning', 'error'];

    private static bool $busy = false;

    /** An action of the signed-in user (the old page rows: "Send To Reviewer No 1", ...). */
    public static function write(Request $request, string $content, string $category = 'editorial', string $level = 'info', array $context = [], ?int $manuscriptId = null): void
    {
        self::event($category, $content, $level, $context, $manuscriptId, null, $request);
    }

    /** A successful upload: file names and sizes go to the context. */
    public static function upload(Request $request, string $content, ?int $manuscriptId = null): void
    {
        self::event('upload', $content, 'info', ['files' => self::files($request)], $manuscriptId, null, $request);
    }

    /** @return list<array{name: string, size: int}> */
    public static function files(Request $request): array
    {
        $out = [];
        $files = $request->allFiles();
        array_walk_recursive($files, function ($f) use (&$out) {
            if ($f instanceof \Illuminate\Http\UploadedFile) {
                $out[] = ['name' => $f->getClientOriginalName(), 'size' => (int) $f->getSize()];
            }
        });

        return array_slice($out, 0, 20);
    }

    public static function event(string $category, string $content, string $level = 'info', array $context = [], ?int $manuscriptId = null,
        ?User $user = null, ?Request $request = null, ?int $status = null): void
    {
        if (self::$busy) {
            return;
        }
        self::$busy = true;
        try {
            $request ??= app()->runningInConsole() ? null : request();
            $user ??= $request?->user();
            DB::table('activity_log')->insert([
                'user' => $user ? trim("$user->first_name $user->last_name").", $user->email" : ($category === 'system' ? '(system)' : '(guest)'),
                'ip' => $request ? (string) $request->ip() : '-',
                'content' => Str::limit($content, 250, ''),
                'logged_at' => now(),
                'category' => $category,
                'level' => $level,
                'user_id' => $user?->id,
                'role' => $user?->role,
                'manuscript_id' => $manuscriptId,
                'method' => $request?->method(),
                'url' => $request ? self::url($request) : null,
                'status' => $status,
                'context' => $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE) : null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Activity log write failed: '.$e->getMessage());
        } finally {
            self::$busy = false;
        }
    }

    /** Unhandled exception (wired in bootstrap/app.php). The full trace stays in laravel.log. */
    public static function exception(\Throwable $e): void
    {
        $mail = (bool) preg_match('/Mail|Transport|Smtp/i', $e::class);
        self::event($mail ? 'mail' : 'error', ($mail ? 'Mail failed: ' : 'Error: ').class_basename($e).': '.Str::limit($e->getMessage(), 150, ''), 'error',
            ['class' => $e::class, 'message' => Str::limit($e->getMessage(), 500, ''), 'at' => basename($e->getFile()).':'.$e->getLine()]);
    }

    /** Path only (no query string, which may carry a confirmation code); one-time tokens in the path are masked. */
    private static function url(Request $request): string
    {
        $path = '/'.ltrim($request->path(), '/');
        $token = $request->route('token');

        return Str::limit(is_string($token) && $token !== '' ? str_replace($token, '***', $path) : $path, 250, '');
    }
}
