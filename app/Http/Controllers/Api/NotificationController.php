<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request, NotificationService $notificationService): JsonResponse
    {
        $this->authorize('viewAny', Notification::class);

        return $this->paginatedResponse(
            $notificationService->getUserNotifications($request->user()->id),
            'Notifications récupérées avec succès.',
            fn ($notification) => new NotificationResource($notification)
        );
    }

    public function unreadCount(Request $request, NotificationService $notificationService): JsonResponse
    {
        return $this->successResponse(
            ['count' => $notificationService->getUnreadCount($request->user()->id)],
            'Compteur des notifications non lues récupéré avec succès.'
        );
    }

    public function show(Notification $notification): JsonResponse
    {
        $this->authorize('view', $notification);

        return $this->successResponse(
            new NotificationResource($notification->load('fromUser')),
            'Détail de la notification récupéré avec succès.'
        );
    }

    public function markAsRead(Notification $notification, NotificationService $notificationService): JsonResponse
    {
        $this->authorize('update', $notification);

        return $this->successResponse(
            new NotificationResource($notificationService->markAsRead($notification)),
            'Notification marquée comme lue.'
        );
    }

    public function markAllAsRead(Request $request, NotificationService $notificationService): JsonResponse
    {
        $updatedCount = $notificationService->markAllAsReadForUser($request->user()->id);

        return $this->successResponse(
            ['updated_count' => $updatedCount],
            'Toutes les notifications ont été marquées comme lues.'
        );
    }

    public function destroy(Request $request, Notification $notification, NotificationService $notificationService): JsonResponse
    {
        $this->authorize('delete', $notification);

        if (!$notificationService->deleteForUser($notification, $request->user()->id)) {
            return $this->errorResponse('Suppression impossible.', 403);
        }

        return $this->successResponse(null, 'Notification supprimée avec succès.');
    }
}
