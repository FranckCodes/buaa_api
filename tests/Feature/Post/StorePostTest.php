<?php

namespace Tests\Feature\Post;

use App\Models\Reference\PostTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class StorePostTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_client_can_create_post(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');
        $tag  = PostTag::firstOrFail();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/posts', [
                'author_id'   => $user->id,
                'content'     => 'Ma première publication BUAA',
                'post_tag_id' => $tag->id,
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('posts', [
            'author_id' => $user->id,
            'content'   => 'Ma première publication BUAA',
        ]);
    }

    public function test_guest_cannot_create_post(): void
    {
        $this->seed();

        $this->postJson('/api/posts', ['content' => 'test'])
            ->assertUnauthorized();
    }
}
