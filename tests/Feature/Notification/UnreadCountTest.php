<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class UnreadCountTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_get_unread_notifications_count(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        Notification::factory()->count(2)->create(['user_id' => $user->id, 'is_read' => false]);
        Notification::factory()->count(1)->create(['user_id' => $user->id, 'is_read' => true]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications/unread-count')
            ->assertOk()
            ->assertJsonPath('data.count', 2);
    }
}
