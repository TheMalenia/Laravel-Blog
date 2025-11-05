<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_post()
    {
        $response = $this->post('/posts', [
            'title' => 'Guest Post',
            'body' => 'Body',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/posts', [
                'title' => 'My Post',
                'body' => 'Post body here',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('posts', ['title' => 'My Post']);
    }

    public function test_authenticated_user_can_update_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['title' => 'Old']);

        $this->actingAs($user)
            ->put('/posts/'.$post->id, [
                'title' => 'Updated',
                'body' => $post->body,
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('posts', ['title' => 'Updated']);
    }

    public function test_authenticated_user_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();

        $this->actingAs($user)
            ->delete('/posts/'.$post->id)
            ->assertStatus(302);

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
