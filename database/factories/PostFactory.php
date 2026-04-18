<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\Reference\PostStatus;
use App\Models\Reference\PostTag;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'author_id'      => User::factory(),
            'content'        => fake()->paragraph(),
            'post_tag_id'    => PostTag::first()?->id,
            'post_status_id' => PostStatus::where('code', 'pending')->first()?->id,
            'likes_count'    => 0,
        ];
    }
}
