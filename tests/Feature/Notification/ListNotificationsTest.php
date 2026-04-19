<?php

namespace Tests\Feature\Notification;

use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\InteractsWithRoles;
use Tests\TestCase;

class ListNotificationsTest extends TestCase
{
    use RefreshDatabase, InteractsWithRoles;

    public function test_user_can_list_own_notifications(): void
    {
        $this->seed();

        $user = $this->createUserWithRole('client');

        Notification::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/notifications')
            ->assertOk()
            ->assertJsonPath('success', true);
    }

    public function test_user_cannot_see_other_user_notifications(): void
    {
        $this->seed();

        $userA = $this->createUserWithRole('client');
        $userB = $this->createUserWithRole('client');

        Notification::factory()->count(3)->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA, 'sanctum')
            ->getJson('/api/notifications');

        $response->assertOk();
        // userA voit 0 notifications (les 3 appartiennent à userB)
        $this->assertCount(0, $response->json('data'));
    }
}
