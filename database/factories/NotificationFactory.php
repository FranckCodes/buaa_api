<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id'      => User::factory(),
            'category'     => fake()->randomElement(['app', 'feed']),
            'type'         => fake()->randomElement(['like', 'comment', 'follow', 'credit', 'insurance', 'membership', 'alert', 'info', 'success']),
            'title'        => fake()->sentence(4),
            'body'         => fake()->sentence(),
            'is_read'      => false,
            'action_label' => null,
            'action_url'   => null,
            'from_user_id' => null,
        ];
    }
}
