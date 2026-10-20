<?php

namespace Tests\Feature;

use App\Mail\ConfirmRegistration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private function legacyUser(array $attrs = []): User
    {
        // sha1('secret') as stored by the old WA_SHA1Encryption
        return User::factory()->create($attrs + [
            'email' => 'Old.User@Example.com',
            'password' => null,
            'legacy_sha1' => sha1('secret'),
            'role' => 'reviewer',
        ]);
    }

    public function test_legacy_sha1_password_logs_in_and_is_rehashed_to_bcrypt(): void
    {
        $user = $this->legacyUser();

        $this->post('/users/login', ['email' => 'old.user@example.com', 'password' => 'secret'])
            ->assertRedirect(route('welcome'));

        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertNull($user->legacy_sha1);
        $this->assertTrue(Hash::check('secret', $user->password));
    }

    public function test_wrong_password_is_rejected_and_keeps_legacy_hash(): void
    {
        $user = $this->legacyUser();

        $this->post('/users/login', ['email' => $user->email, 'password' => 'nope'])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNotNull($user->refresh()->legacy_sha1);
    }

    public function test_duplicate_email_logs_in_the_account_whose_password_matches(): void
    {
        $this->legacyUser(['id' => 1]);
        $second = $this->legacyUser(['id' => 2, 'legacy_sha1' => sha1('other')]);

        $this->post('/users/login', ['email' => 'old.user@example.com', 'password' => 'other']);

        $this->assertAuthenticatedAs($second);
    }

    public function test_unconfirmed_user_cannot_log_in(): void
    {
        $this->legacyUser(['email_verified_at' => null]);

        $this->post('/users/login', ['email' => 'old.user@example.com', 'password' => 'secret'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_register_sends_confirmation_and_confirm_enables_login(): void
    {
        Mail::fake();

        $this->post('/users/register', [
            'email' => 'new@example.com', 'password' => 'abcdef', 'password_confirmation' => 'abcdef',
            'first_name' => 'Ann', 'last_name' => 'Lee', 'address' => 'Main 1', 'city' => 'Sofia', 'country' => 'BG',
        ])->assertRedirect(route('thank-you'));

        $user = User::where('email', 'new@example.com')->firstOrFail();
        $this->assertSame('author', $user->role);
        $this->assertNull($user->email_verified_at);
        Mail::assertQueued(ConfirmRegistration::class, fn ($m) => $m->hasTo('new@example.com'));

        $this->post('/users/login', ['email' => 'new@example.com', 'password' => 'abcdef'])->assertSessionHasErrors('email');

        $this->get('/users/confirm?UID='.$user->id.'&code=wrong')->assertNotFound();
        $this->get('/users/confirm?UID='.$user->id.'&code='.$user->verification_code)->assertOk();
        $this->assertNotNull($user->refresh()->email_verified_at);

        $this->post('/users/login', ['email' => 'new@example.com', 'password' => 'abcdef'])->assertRedirect(route('welcome'));
    }

    public function test_register_rejects_existing_email_case_insensitively(): void
    {
        $this->legacyUser();

        $this->post('/users/register', [
            'email' => 'OLD.user@example.com', 'password' => 'abcdef', 'password_confirmation' => 'abcdef',
            'first_name' => 'A', 'last_name' => 'B', 'address' => 'x', 'city' => 'y', 'country' => 'z',
        ])->assertSessionHasErrors('email');
    }

    public function test_forgot_password_sends_a_reset_link_never_a_password(): void
    {
        Notification::fake();
        $user = $this->legacyUser();

        $this->post('/profile/forgot-password', ['email' => $user->email])->assertSessionHas('status');

        Notification::assertSentTo($user, ResetPassword::class, function ($n) use ($user) {
            $token = $n->token;
            $this->post('/profile/reset-password', [
                'token' => $token, 'email' => $user->email, 'password' => 'brandnew', 'password_confirmation' => 'brandnew',
            ])->assertRedirect(route('login'));

            return true;
        });

        $user->refresh();
        $this->assertNull($user->legacy_sha1);
        $this->assertTrue(Hash::check('brandnew', $user->password));
    }

    public function test_profile_and_change_password_require_login_and_log_activity(): void
    {
        $this->get('/profile')->assertRedirect(route('login'));

        $user = $this->legacyUser();
        $this->actingAs($user)
            ->post('/profile/change-password', ['password' => 'changed1', 'password_confirmation' => 'changed1'])
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('changed1', $user->refresh()->password));
        $this->assertDatabaseHas('activity_log', ['content' => 'Change Password']);

        $this->actingAs($user)->post('/profile', ['first_name' => 'Zed', 'last_name' => 'Q'])->assertSessionHasNoErrors();
        $this->assertSame('Zed', $user->refresh()->first_name);
    }
}
