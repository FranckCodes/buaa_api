<?php

namespace Database\Factories;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id'     => Conversation::factory(),
            'sender_id'           => User::factory(),
            'text'                => fake()->sentence(),
            'type'                => 'text',
            'image_url'           => null,
            'file_url'            => null,
            'reply_to_message_id' => null,
            'status'              => 'sent',
            'deleted_at'          => null,
        ];
    }
}
