<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\PostSave;
use App\Models\Reference\PostStatus;

class PostService
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function createPost(array $data): Post
    {
        $status = PostStatus::where('code', 'pending')->firstOrFail();

        return Post::create([
            'author_id'      => $data['author_id'],
            'content'        => $data['content'],
            'post_tag_id'    => $data['post_tag_id'],
            'post_status_id' => $status->id,
        ]);
    }

    public function approvePost(Post $post, int $validatorId): Post
    {
        $status = PostStatus::where('code', 'approved')->firstOrFail();

        $post->update([
            'post_status_id' => $status->id,
            'valide_par'     => $validatorId,
            'motif_rejet'    => null,
        ]);

        return $post->fresh('status');
    }

    public function rejectPost(Post $post, int $validatorId, ?string $reason): Post
    {
        $status = PostStatus::where('code', 'rejected')->firstOrFail();

        $post->update([
            'post_status_id' => $status->id,
            'valide_par'     => $validatorId,
            'motif_rejet'    => $reason,
        ]);

        return $post->fresh('status');
    }

    public function toggleLike(Post $post, int $userId): array
    {
        $existing = PostLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            $post->decrement('likes_count');

            return ['liked' => false, 'likes_count' => max(0, $post->fresh()->likes_count)];
        }

        PostLike::create(['post_id' => $post->id, 'user_id' => $userId]);
        $post->increment('likes_count');

        if ($post->author_id !== $userId) {
            $this->notificationService->create([
                'user_id'      => $post->author_id,
                'category'     => 'feed',
                'type'         => 'like',
                'title'        => 'Nouveau like',
                'body'         => 'Votre publication a reçu un like.',
                'from_user_id' => $userId,
            ]);
        }

        return ['liked' => true, 'likes_count' => $post->fresh()->likes_count];
    }

    public function toggleSave(Post $post, int $userId): array
    {
        $existing = PostSave::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($existing) {
            $existing->delete();
            return ['saved' => false];
        }

        PostSave::create(['post_id' => $post->id, 'user_id' => $userId]);

        return ['saved' => true];
    }

    public function addComment(Post $post, int $userId, string $text): Comment
    {
        $comment = Comment::create([
            'post_id'   => $post->id,
            'author_id' => $userId,
            'text'      => $text,
        ]);

        if ($post->author_id !== $userId) {
            $this->notificationService->create([
                'user_id'      => $post->author_id,
                'category'     => 'feed',
                'type'         => 'comment',
                'title'        => 'Nouveau commentaire',
                'body'         => 'Quelqu\'un a commenté votre publication.',
                'from_user_id' => $userId,
            ]);
        }

        return $comment;
    }
}
