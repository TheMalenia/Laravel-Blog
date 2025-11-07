<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_logs_in_registered_user_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'login@gmail.com',
            'password' => bcrypt('testpass1383'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'login@gmail.com',
            'password' => 'testpass1383',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'login@gmail.com']);
    }

    public function test_login_fails_if_email_does_not_exist()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noone@example.com',
            'password' => 'somepassword',
        ]);

        $this->assertContains($response->getStatusCode(), [401, 422]);
        $this->assertGuest();
    }

    public function test_login_fails_if_password_is_wrong()
    {
        $user = User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => bcrypt('correct-password'),
        ]);
        $response = $this->postJson('/api/login', [
            'email' => 'wrongpass@example.com',
            'password' => 'incorrect',
        ]);

        $this->assertContains($response->getStatusCode(), [401, 422]);
        $this->assertGuest();
    }

    public function test_login_shows_proper_error_message()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noone@example.com',
            'password' => 'whatever',
        ]);

        $this->assertContains($response->getStatusCode(), [401, 422]);

        $payload = $response->json();
        $this->assertTrue(isset($payload['message']) || isset($payload['errors']));
        if (isset($payload['errors'])) {
            $this->assertArrayHasKey('email', $payload['errors']);
        }
    }
}
