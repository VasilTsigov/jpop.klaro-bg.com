<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Editors' administration (old users/users_Index*, users_ChangeStatus, profile_Users, Logos/logos_Index):
 * the user list with role filter, one user's profile + role change, and the activity log. Editors only (route middleware).
 * LEGACY: "Empty logos" (wipes the audit trail with one click) is intentionally not carried over.
 */
class AdminController extends Controller
{
    private const ROLES = ['editor' => 'Editors', 'reviewer' => 'Reviewers', 'author' => 'Authors'];

    public function users(Request $request)
    {
        $role = $request->query('role');
        abort_unless($role === null || isset(self::ROLES[$role]), 404);

        $counts = User::selectRaw('role, count(*) as n')->groupBy('role')->pluck('n', 'role');
        $users = User::when($role, fn ($q) => $q->where('role', $role))
            ->orderByDesc('id')
            ->paginate(25)->withQueryString();
        $roles = self::ROLES;

        return view('editor.users', compact('users', 'role', 'roles', 'counts'));
    }

    public function user(User $user)
    {
        $roles = self::ROLES;

        return view('editor.user', compact('user', 'roles'));
    }

    public function role(Request $request, User $user)
    {
        $data = $request->validate(['role' => ['required', Rule::in(array_keys(self::ROLES))]]);
        if ($user->id === $request->user()->id) {
            return back()->withErrors('You cannot change your own role.');
        }

        $user->forceFill(['role' => $data['role']])->save();
        ActivityLog::write($request, "Change User Role: {$user->email} -> {$data['role']}");

        return back()->with('status', 'Role changed.');
    }

    public function logs(Request $request)
    {
        $f = $request->validate([
            'category' => ['nullable', Rule::in(array_keys(ActivityLog::CATEGORIES))],
            'level' => ['nullable', Rule::in(ActivityLog::LEVELS)],
            'user' => 'nullable|string|max:100',
            'manuscript' => 'nullable|integer|min:1',
            'from' => 'nullable|date',
            'to' => 'nullable|date',
            'q' => 'nullable|string|max:100',
        ]);
        $query = $this->logQuery($f);

        if ($request->query('export') === 'csv') {
            return $this->exportLogs($query);
        }

        $logs = $query->paginate(50)->withQueryString();
        $categories = ActivityLog::CATEGORIES;

        return view('editor.logs', compact('logs', 'f', 'categories'));
    }

    private function logQuery(array $f)
    {
        $like = fn (string $v) => '%'.addcslashes($v, '%_\\').'%';

        return DB::table('activity_log')
            ->when($f['category'] ?? null, fn ($q, $v) => $q->where('category', $v))
            ->when($f['level'] ?? null, fn ($q, $v) => $q->where('level', $v))
            ->when($f['user'] ?? null, fn ($q, $v) => $q->where('user', 'like', $like($v)))
            ->when($f['manuscript'] ?? null, fn ($q, $v) => $q->where('manuscript_id', $v))
            ->when($f['from'] ?? null, fn ($q, $v) => $q->where('logged_at', '>=', Carbon::parse($v)->startOfDay()))
            ->when($f['to'] ?? null, fn ($q, $v) => $q->where('logged_at', '<=', Carbon::parse($v)->endOfDay()))
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where('content', 'like', $like($v)))
            ->orderByDesc('id');
    }

    /** The filtered view as CSV (capped, so that an unfiltered export cannot exhaust the server). */
    private function exportLogs($query): StreamedResponse
    {
        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['id', 'time', 'category', 'level', 'user', 'role', 'ip', 'action', 'manuscript', 'method', 'url', 'status', 'context']);
            // cells that start like a formula are prefixed, so that a spreadsheet does not run them
            $safe = fn ($v) => is_string($v) && preg_match('/^[=+\-@\t\r]/', $v) ? "'".$v : $v;
            foreach ((clone $query)->limit(50000)->cursor() as $l) {
                fputcsv($out, array_map($safe, [$l->id, $l->logged_at, $l->category, $l->level, $l->user, $l->role, $l->ip, $l->content,
                    $l->manuscript_id, $l->method, $l->url, $l->status, $l->context]));
            }
            fclose($out);
        }, 'jpop-logs-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
