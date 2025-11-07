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

        $response = $this->post('/login', [
            'email' => 'login@gmail.com',
            'password' => 'testpass1383',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($user);
        $this->assertContains($response->getStatusCode(), [200, 302]);
    }

    public function test_login_fails_if_email_does_not_exist()
    {
        $response = $this->post('/login', [
            'email' => 'noone@example.com',
            'password' => 'somepassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_fails_if_password_is_wrong()
    {
        $user = User::factory()->create([
            'email' => 'wrongpass@example.com',
            'password' => bcrypt('correct-password'),
        ]);

        $response = $this->post('/login', [
            'email' => 'wrongpass@example.com',
            'password' => 'incorrect',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_shows_proper_error_message()
    {
        $response = $this->post('/login', [
            'email' => 'noone@example.com',
            'password' => 'whatever',
        ]);

        $response->assertSessionHasErrors('email');

        $errors = session('errors');
        $this->assertNotNull($errors);
        $this->assertTrue($errors->has('email'));

        $first = $errors->first('email');
        $this->assertIsString($first);
        $this->assertNotEmpty($first);
    }
}
