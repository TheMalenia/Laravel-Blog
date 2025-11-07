<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registers_new_user_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        // API-first: expect JSON response and a 201 or 200 status for created
        $this->assertContains($response->getStatusCode(), [200, 201]);

        // ensure user was created in DB
        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com']);
    }

    public function test_stores_user_in_database()
    {
        $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ])->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Bad Email',
            'email' => 'not-an-email',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

    public function test_registration_fails_if_email_already_exists()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);
        $response = $this->postJson('/api/register', [
            'name' => 'Dupe',
            'email' => 'duplicate@example.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('email');
        $this->assertEquals(1, User::where('email', 'duplicate@example.com')->count());
    }

    public function test_registration_fails_with_short_password()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Short',
            'email' => 'short@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'short@example.com']);
    }

    public function test_registration_fails_when_password_confirmation_mismatch()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Mismatch',
            'email' => 'mismatch@example.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'different',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_registration_returns_validation_errors()
    {
        $response = $this->postJson('/api/register', [
            // empty payload to trigger validation
        ]);

        $response->assertStatus(422);
        $response->assertJsonStructure(['errors']);
    }
}
