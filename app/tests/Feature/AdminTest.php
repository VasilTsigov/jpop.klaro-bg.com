<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_editors_get_users_and_logs(): void
    {
        $target = User::factory()->create(['role' => 'author']);
        foreach (['author', 'reviewer'] as $role) {
            $this->actingAs(User::factory()->create(['role' => $role]));
            foreach (['/editor/users', '/editor/logs', "/editor/users/{$target->id}"] as $url) {
                $this->get($url)->assertForbidden();
            }
            $this->post("/editor/users/{$target->id}/role", ['role' => 'editor'])->assertForbidden();
        }
        $this->assertSame('author', $target->fresh()->role);
    }

    public function test_guest_is_sent_to_login(): void
    {
        $this->get('/editor/users')->assertRedirect('/users/login');
        $this->get('/editor/logs')->assertRedirect('/users/login');
    }

    public function test_editor_sees_the_menu_and_the_user_list_with_role_filter(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        User::factory()->create(['role' => 'reviewer', 'email' => 'rev@example.com']);
        User::factory()->create(['role' => 'author', 'email' => 'aut@example.com']);

        $this->actingAs($editor)->get('/editor/users')
            ->assertOk()->assertSee('All Users - 3')->assertSee('rev@example.com')->assertSee('aut@example.com')
            ->assertSee('href="'.route('editor.logs').'"', false);
        $this->get('/editor/users?role=reviewer')->assertOk()->assertSee('rev@example.com')->assertDontSee('aut@example.com');
        $this->get('/editor/users?role=admin')->assertNotFound();
    }

    public function test_users_list_is_paginated(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        User::factory()->count(30)->create(['role' => 'author']);

        $this->actingAs($editor)->get('/editor/users')->assertOk()->assertSee('page=2');
        $this->get('/editor/users?page=2')->assertOk();
    }

    public function test_editor_changes_a_role_and_it_is_logged(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        $target = User::factory()->create(['role' => 'author', 'email' => 'x@example.com']);

        $this->actingAs($editor)->get("/editor/users/{$target->id}")->assertOk()->assertSee('x@example.com');
        $this->post("/editor/users/{$target->id}/role", ['role' => 'reviewer'])->assertSessionHas('status');
        $this->assertSame('reviewer', $target->fresh()->role);
        $this->assertTrue(DB::table('activity_log')->where('content', 'like', 'Change User Role: x@example.com%')->exists());

        $this->post("/editor/users/{$target->id}/role", ['role' => 'admin'])->assertSessionHasErrors('role');
        $this->post("/editor/users/{$editor->id}/role", ['role' => 'author'])->assertSessionHasErrors();
        $this->assertSame('editor', $editor->fresh()->role);
    }

    public function test_logs_are_listed_newest_first_and_paginated(): void
    {
        $editor = User::factory()->create(['role' => 'editor']);
        foreach (range(1, 55) as $i) {
            DB::table('activity_log')->insert(['id' => $i, 'user' => 'U', 'ip' => '1.2.3.4', 'content' => "Action $i", 'logged_at' => now()]);
        }

        $this->actingAs($editor)->get('/editor/logs')->assertOk()->assertSee('Action 55')->assertSee('Action 6<', false)->assertDontSee('Action 5<', false)->assertSee('page=2');
        $this->get('/editor/logs?page=2')->assertOk()->assertSee('Action 5<', false);
    }
}
