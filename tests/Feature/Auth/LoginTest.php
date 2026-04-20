<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_valid_credentials(): void
    {
        $this->seed();

        $user = User::factory()->create(['password' => bcrypt('password')]);

        $this->postJson('/api/auth/login', [
            'email'    => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success', 'message',
                'data' => ['user', 'token'],
            ]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        $this->seed();

        $this->postJson('/api/auth/login', [
            'email'    => 'wrong@example.com',
            'password' => 'bad-password',
        ])->assertStatus(422)->assertJsonPath('success', false);
    }
}
