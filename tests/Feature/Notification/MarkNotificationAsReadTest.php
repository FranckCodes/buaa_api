<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class MarkNotificationAsReadTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_mark_notification_as_read(): void
    {
        $this->seed();

        $user         = $this->createUserWithRole('client');
        $notification = Notification::factory()->create(['user_id' => $user->id, 'is_read' => false]);

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('notifications', ['id' => $notification->id, 'is_read' => true]);
    }

    public function test_user_cannot_mark_other_user_notification_as_read(): void
    {
        $this->seed();

        $userA        = $this->createUserWithRole('client');
        $userB        = $this->createUserWithRole('client');
        $notification = Notification::factory()->create(['user_id' => $userB->id, 'is_read' => false]);

        $this->actingAs($userA, 'sanctum')
            ->postJson("/api/notifications/{$notification->id}/read")
            ->assertForbidden();
    }

    public function test_user_can_mark_all_notifications_as_read(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');
        Notification::factory()->count(3)->create(['user_id' => $user->id, 'is_read' => false]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('data.updated_count', 3);

        $this->assertEquals(0, Notification::where('user_id', $user->id)->where('is_read', false)->count());
    }
}
