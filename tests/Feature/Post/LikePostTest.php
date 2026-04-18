<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class LikePostTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_like_post(): void
    {
        $this->seed();

        $author = $this->createUserWithRole('client');
        $user   = $this->createUserWithRole('client');
        $post   = Post::factory()->create(['author_id' => $author->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/posts/{$post->id}/like", ['user_id' => $user->id])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }
}
