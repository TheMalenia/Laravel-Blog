<?php

namespace Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_correct_data()
    {
        $user = User::factory()->create([
            'email' => 'login@email.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user['email'],
            'password' => 'password',
        ]);

        $response->assertStatus(200);
        // check that after implementing login
        $response->assertJsonStructure(['token', 'user' => ['id','email']]);
    }

    public function test_login_with_invalid_data()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => 'invalid@email.com',
            'password' => 'password',
        ]);

        // $response->assertJson ...
        $this->assertContains($response->getStatusCode(), [401, 422]);
        $this->assertGuest();
    }

    public function test_login_with_wrong_password()
    {
        $user = User::factory()->create([
            'email' => 'login@email.com',
            'password' => 'password',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => $user['email'],
            'password' => 'wrong-password',
        ]);

        // $response->assertJson ...
        $this->assertContains($response->getStatusCode(), [401, 422]);
        $this->assertGuest();
    }
}
