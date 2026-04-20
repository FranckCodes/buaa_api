<?php

namespace Tests\Feature\Post;

use App\Models\Post;
use App\Models\Reference\PostStatus;
use App\Models\Reference\PostTag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ModeratePostTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_admin_can_approve_post(): void
    {
        $this->seed();

        $admin  = $this->createUserWithRole('admin');
        $author = $this->createUserWithRole('client');

        $post = Post::create([
            'author_id'      => $author->id,
            'content'        => 'Publication test',
            'post_tag_id'    => PostTag::firstOrFail()->id,
            'post_status_id' => PostStatus::where('code', 'pending')->firstOrFail()->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/posts/{$post->id}/moderate", [
                'action'       => 'approve',
                'validator_id' => $admin->id,
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertEquals('approved', $post->fresh()->status->code);
    }

    public function test_admin_can_reject_post_with_reason(): void
    {
        $this->seed();

        $admin  = $this->createUserWithRole('admin');
        $author = $this->createUserWithRole('client');

        $post = Post::create([
            'author_id'      => $author->id,
            'content'        => 'Publication test',
            'post_tag_id'    => PostTag::firstOrFail()->id,
            'post_status_id' => PostStatus::where('code', 'pending')->firstOrFail()->id,
        ]);

        $this->actingAs($admin, 'sanctum')
            ->postJson("/api/posts/{$post->id}/moderate", [
                'action'       => 'reject',
                'validator_id' => $admin->id,
                'reason'       => 'Contenu inapproprié.',
            ])
            ->assertOk();

        $this->assertEquals('rejected', $post->fresh()->status->code);
        $this->assertEquals('Contenu inapproprié.', $post->fresh()->motif_rejet);
    }

    public function test_client_cannot_moderate_post(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');
        $post = Post::factory()->create(['author_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/posts/{$post->id}/moderate", [
                'action'       => 'approve',
                'validator_id' => $user->id,
            ])
            ->assertForbidden();
    }
}
