<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_with_correct_data()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // $response->assertJson ...
        $response->assertSuccessful();
        $this->assertDatabaseHas('users', ['email' => 'test@email.com']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // $response->assertJson ...
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

    public function test_registration_fails_with_invalid_match_password()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => 'password',
            'password_confirmation' => 'not-an-password',
        ]);

        // $response->assertJson ...
        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_registration_fails_with_duplicate_mail()
    {
        $user = User::factory()->create(['email' => 'test@email.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // $response->assertJson ...
        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
        $this->assertEquals(1, User::where('email', 'test@email.com')->count());
    }

    public function test_registration_fails_with_short_password()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@email.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        // $response->assertJson ...
        $response->assertStatus(422)
            ->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'test@email.com']);
    }


}
