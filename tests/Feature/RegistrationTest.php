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
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $response->assertSessionHasNoErrors();

        $this->assertContains($response->getStatusCode(), [200, 302]);

        $this->assertAuthenticated();
    }

    public function test_stores_user_in_database()
    {
        $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ])->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', ['email' => 'test@gmail.com']);
    }

    public function test_registration_fails_with_invalid_email()
    {
        $response = $this->post('/register', [
            'name' => 'Bad Email',
            'email' => 'not-an-email',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseMissing('users', ['email' => 'not-an-email']);
    }

    public function test_registration_fails_if_email_already_exists()
    {
        User::factory()->create(['email' => 'duplicate@example.com']);

        $response = $this->post('/register', [
            'name' => 'Dupe',
            'email' => 'duplicate@example.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'testpass1383',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertEquals(1, User::where('email', 'duplicate@example.com')->count());
    }

    public function test_registration_fails_with_short_password()
    {
        $response = $this->post('/register', [
            'name' => 'Short',
            'email' => 'short@example.com',
            'password' => '123',
            'password_confirmation' => '123',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'short@example.com']);
    }

    public function test_registration_fails_when_password_confirmation_mismatch()
    {
        $response = $this->post('/register', [
            'name' => 'Mismatch',
            'email' => 'mismatch@example.com',
            'password' => 'testpass1383',
            'password_confirmation' => 'different',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'mismatch@example.com']);
    }

    public function test_registration_returns_validation_errors()
    {
        $response = $this->post('/register', [
            // this is a empty payload to trigger validation and gets the error
        ]);

        $response->assertSessionHasErrors();
    }
}
