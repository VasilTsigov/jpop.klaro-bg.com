<?php

namespace App\Http\Controllers;

use App\Mail\ConfirmRegistration;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate(['email' => 'required|string', 'password' => 'required|string']);

        // LEGACY: 4 e-mails are duplicated and MySQL compared case-insensitively -> try every match
        $candidates = User::whereRaw('lower(email) = ?', [mb_strtolower(trim($data['email']))])->get();
        $user = $candidates->first(fn (User $u) => $this->passwordMatches($u, $data['password']));

        if (! $user) {
            throw ValidationException::withMessages(['email' => 'Wrong email or password.']);
        }
        if (! $user->email_verified_at) {
            throw ValidationException::withMessages(['email' => 'Please confirm your registration first (see the email we sent you).']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended(route('welcome'));
    }

    /** Current bcrypt hash first; otherwise the legacy SHA1 (re-hashed to bcrypt on success, L4). */
    private function passwordMatches(User $user, string $plain): bool
    {
        if ($user->password && Hash::check($plain, $user->password)) {
            return true;
        }
        $sha1 = trim((string) $user->legacy_sha1);
        if ($sha1 !== '' && hash_equals(strtolower($sha1), sha1($plain))) {
            $user->forceFill(['password' => $plain, 'legacy_sha1' => null])->save();

            return true;
        }

        return false;
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // LEGACY: a captcha guarded this form; replaced by throttling (route middleware)
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255', function ($attr, $value, $fail) {
                if (User::whereRaw('lower(email) = ?', [mb_strtolower($value)])->exists()) {
                    $fail('This email is already registered.');
                }
            }],
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'address' => 'required|string|max:100',
            'city' => 'required|string|max:90',
            'country' => 'required|string|max:20',
        ]);

        $user = DB::transaction(function () use ($data, $request) {
            // id is the legacy UserID (non-incrementing column) -> continue the sequence
            $user = new User($data);
            $user->forceFill([
                'id' => (int) User::max('id') + 1,
                'role' => 'author',
                'verification_code' => Str::random(20),
                'registration_ip' => $request->ip(),
                'registered_at' => now(),
            ])->save();

            return $user;
        });

        Mail::to($user->email)->send(new ConfirmRegistration($user));

        return redirect()->route('thank-you');
    }

    public function confirm(Request $request)
    {
        $user = User::find((int) $request->query('UID'));
        $code = (string) $request->query('code');

        if (! $user || ! $user->verification_code || ! hash_equals($user->verification_code, $code)) {
            abort(404);
        }

        // LEGACY: confirming set UserLavel=1 and replaced the code with a new random one
        $user->forceFill([
            'email_verified_at' => $user->email_verified_at ?? now(),
            'verification_code' => Str::random(20),
        ])->save();

        return view('auth.confirmed');
    }

    public function showForgot()
    {
        return view('auth.forgot');
    }

    /** LEGACY: the old page generated a new password and e-mailed it in clear text (L4) -> reset link instead. */
    public function sendReset(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        Password::sendResetLink($request->only('email'));

        // same answer whether the address exists or not
        return back()->with('status', 'If this email is registered, a link to set a new password was sent to it.');
    }

    public function showReset(string $token)
    {
        return view('auth.reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $data = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', PasswordRule::min(6)],
        ]);

        $status = Password::reset($data + ['password_confirmation' => $request->password_confirmation], function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'legacy_sha1' => null])->save();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return redirect()->route('login')->with('status', 'Your password was changed. You can log in.');
    }
}
