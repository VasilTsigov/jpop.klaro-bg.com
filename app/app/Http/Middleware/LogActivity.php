<?php

namespace App\Http\Middleware;

use App\Models\Manuscript;
use App\Models\ManuscriptFile;
use App\Models\ManuscriptReview;
use App\Support\ActivityLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * Journal entries that come from the request itself (docs/03-plan.md §9): what a signed-in user opened,
 * refused requests (403/404/419/413/429) and uploads that were rejected by validation.
 */
class LogActivity
{
    /** Pages that are not worth a row (the journal itself, so that browsing it does not fill it). */
    private const SKIP = ['editor.logs'];

    private const LABELS = [
        'files.show' => 'Open file', 'manuscripts.text' => 'Open abstract / cover letter', 'manuscripts.index' => 'Open My manuscripts',
        'manuscripts.create' => 'Open Submit manuscript', 'reviews.index' => 'Open Manuscripts for review', 'editor.list' => 'Open manuscript list',
        'editor.users' => 'Open Users', 'editor.user' => 'Open user', 'editor.search' => 'Open Search', 'editor.status' => 'Open manuscript status',
        'editor.assign' => 'Open reviewer assignment', 'editor.reminder' => 'Open reminder', 'editor.decision' => 'Open decision',
        'editor.reject' => 'Open reject', 'editor.proof' => 'Open proof', 'profile' => 'Open Profile', 'welcome' => 'Open Home',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        try {
            $this->log($request, $response);
        } catch (\Throwable) {
            // the journal must never break a request
        }

        return $response;
    }

    private function log(Request $request, Response $response): void
    {
        $status = $response->getStatusCode();
        $user = $request->user();
        $manuscriptId = $this->manuscriptId($request);

        if ($status >= 400 && $status < 500) {
            [$category, $text] = match (true) {
                $status === 429 => ['auth', 'Too many requests'],
                $status === 413 => ['upload', 'Upload too large'],
                $status === 419 => ['error', 'Page expired (or the upload exceeds the server limit)'],
                $status === 404 && ! $user => [null, null], // bots probing the site: not worth a row
                default => ['security', $status === 403 ? 'Access denied' : 'Not found'],
            };
            if ($category) {
                ActivityLog::event($category, "$text (HTTP $status)", 'warning', [], $manuscriptId, null, $request, $status);
            }

            return;
        }

        if (! $user) {
            return;
        }

        // an upload the validator refused: the redirect back carries the errors in the session
        if ($request->isMethod('POST') && $request->allFiles() && $response->isRedirection() && $request->hasSession()) {
            $errors = $request->session()->get('errors')?->all();
            if ($errors) {
                ActivityLog::event('upload', 'Upload refused: '.$errors[0], 'warning', [
                    'files' => ActivityLog::files($request), 'errors' => array_slice($errors, 0, 5),
                ], $manuscriptId, null, $request, $status);
            }

            return;
        }

        $route = $request->route()?->getName();
        if ($request->isMethod('GET') && $status === 200 && $route && ! in_array($route, self::SKIP, true)) {
            $url = $request->path();
            if (! Cache::add("log-view:{$user->id}:$url", 1, 60)) {
                return; // the same page again within a minute (reload, pagination back and forth)
            }
            $what = $request->route('file')?->original_name;
            ActivityLog::event('view', (self::LABELS[$route] ?? "Open $route").($what ? ": $what" : ''), 'info',
                array_filter(['route' => $route, 'page' => $request->query('page')]), $manuscriptId, null, $request, $status);
        }
    }

    private function manuscriptId(Request $request): ?int
    {
        $route = $request->route();
        if (! $route) {
            return null;
        }
        foreach (['manuscript', 'file', 'review'] as $param) {
            $m = $route->parameter($param);
            if ($m instanceof Manuscript) {
                return $m->id;
            }
            if ($m instanceof ManuscriptFile || $m instanceof ManuscriptReview) {
                return $m->manuscript_id;
            }
        }

        return null;
    }
}
