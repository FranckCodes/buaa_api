<?php

namespace App\Services;

use App\Models\Notification;

class NotificationService
{
    public function create(array $data): Notification
    {
        return Notification::create([
            'user_id'      => $data['user_id'],
            'category'     => $data['category'],
            'type'         => $data['type'],
            'title'        => $data['title'],
            'body'         => $data['body'] ?? null,
            'is_read'      => false,
            'action_label' => $data['action_label'] ?? null,
            'action_url'   => $data['action_url'] ?? null,
            'from_user_id' => $data['from_user_id'] ?? null,
        ]);
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['is_read' => true]);
        return $notification->fresh();
    }

    public function markAllAsReadForUser(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    }
}
