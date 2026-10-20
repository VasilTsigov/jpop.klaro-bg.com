<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password as PasswordRule;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('auth.profile');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'city' => 'nullable|string|max:90',
            'country' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:100',
        ]);
        $request->user()->update($data);
        $this->log($request, 'Edit Profile');

        return back()->with('status', 'Profile saved.');
    }

    public function showPassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate(['password' => ['required', 'confirmed', PasswordRule::min(6)]]);
        $request->user()->forceFill(['password' => $data['password'], 'legacy_sha1' => null])->save();
        $this->log($request, 'Change Password');

        return back()->with('status', 'Password changed.');
    }

    /** Same activity_log rows the old pages wrote (logID is not auto-increment in the new schema). */
    private function log(Request $request, string $content): void
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
