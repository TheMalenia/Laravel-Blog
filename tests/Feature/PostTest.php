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
        $response = $this->postJson('/api/posts', [
            'title' => 'Guest Post',
            'body' => 'Body',
        ]);

        $response->assertStatus(401);
    }

    public function test_authenticated_user_can_create_post()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')
            ->postJson('/api/posts', [
                'title' => 'My Post',
                'body' => 'Post body here',
            ]);

        $this->assertContains($response->getStatusCode(), [200, 201]);
        $this->assertDatabaseHas('posts', ['title' => 'My Post']);
    }

    public function test_authenticated_user_can_update_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create(['title' => 'Old']);
        $response = $this->actingAs($user, 'api')
            ->putJson('/api/posts/'.$post->id, [
                'title' => 'Updated',
                'body' => $post->body,
            ]);

        $this->assertContains($response->getStatusCode(), [200, 204]);
        $this->assertDatabaseHas('posts', ['title' => 'Updated']);
    }

    public function test_authenticated_user_can_delete_post()
    {
        $user = User::factory()->create();
        $post = Post::factory()->for($user)->create();
        $response = $this->actingAs($user, 'api')
            ->deleteJson('/api/posts/'.$post->id);

        $this->assertContains($response->getStatusCode(), [200, 204]);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }
}
