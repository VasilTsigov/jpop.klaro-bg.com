<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_see_the_public_home_page(): void
    {
        $this->get('/')->assertOk()->assertSee('Online Manuscript Submission, Review and Tracking System');
    }

    public function test_logged_in_users_go_to_welcome(): void
    {
        $this->actingAs(User::factory()->create())->get('/')->assertRedirect(route('welcome'));
    }
}
