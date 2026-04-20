<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\Reference\PostStatus;
use App\Models\Reference\PostTag;
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

        $post = Post::create([
            'author_id'      => $author->id,
            'content'        => 'Bonjour BUAA',
            'post_tag_id'    => PostTag::firstOrFail()->id,
            'post_status_id' => PostStatus::where('code', 'pending')->firstOrFail()->id,
            'likes_count'    => 0,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/posts/{$post->id}/like", ['user_id' => $user->id])
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.liked', true);

        $this->assertDatabaseHas('post_likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }

    public function test_user_can_unlike_post(): void
    {
        $this->seed();

        $author = $this->createUserWithRole('client');
        $user   = $this->createUserWithRole('client');

        $post = Post::create([
            'author_id'      => $author->id,
            'content'        => 'Bonjour BUAA',
            'post_tag_id'    => PostTag::firstOrFail()->id,
            'post_status_id' => PostStatus::where('code', 'pending')->firstOrFail()->id,
            'likes_count'    => 1,
        ]);

        // Like d'abord
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/posts/{$post->id}/like", ['user_id' => $user->id]);

        // Unlike
        $this->actingAs($user, 'sanctum')
            ->postJson("/api/posts/{$post->id}/like", ['user_id' => $user->id])
            ->assertOk()
            ->assertJsonPath('data.liked', false);

        $this->assertDatabaseMissing('post_likes', ['post_id' => $post->id, 'user_id' => $user->id]);
    }
}
