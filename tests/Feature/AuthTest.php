<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_creates_user()
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com']);
    }

    public function test_login_with_valid_credentials_authenticates()
    {
        $user = User::factory()->create([
            'email' => 'login@gmail.com',
            'password' => bcrypt('testpass1383'),
        ]);

        $response = $this->post('/login', [
            'email' => 'login@gmail.com',
            'password' => 'testpass1383',
        ]);

        $this->assertAuthenticatedAs($user);
    }
}
