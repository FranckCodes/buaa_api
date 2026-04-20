<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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

    public function getUserNotifications(string $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Notification::with('fromUser')
            ->where('user_id', $userId)
            ->latest()
            ->paginate($perPage);
    }

    public function getUnreadCount(string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public function markAsRead(Notification $notification): Notification
    {
        $notification->update(['is_read' => true]);

        return $notification->fresh('fromUser');
    }

    public function markAllAsReadForUser(string $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'updated_at' => now()]);
    }

    public function deleteForUser(Notification $notification, string $userId): bool
    {
        if ($notification->user_id !== $userId) {
            return false;
        }

        return (bool) $notification->delete();
    }
}
