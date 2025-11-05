<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $this->actingAs($user)
            ->post('/posts/'.$post->id.'/comments', [
                'body' => 'Nice post',
            ])
            ->assertStatus(302);

        $this->assertDatabaseHas('comments', ['body' => 'Nice post', 'post_id' => $post->id]);
    }

    public function test_guest_cannot_create_comment()
    {
        $post = Post::factory()->create();

        $this->post('/posts/'.$post->id.'/comments', [
            'body' => 'I comment',
        ])->assertRedirect('/login');
    }
}
