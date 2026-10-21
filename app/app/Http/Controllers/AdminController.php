<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

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

    public function logs()
    {
        $logs = DB::table('activity_log')->orderByDesc('id')->paginate(50);

        return view('editor.logs', compact('logs'));
    }
}
