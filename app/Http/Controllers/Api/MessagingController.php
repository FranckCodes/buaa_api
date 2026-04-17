<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\MarkConversationAsReadRequest;
use App\Http\Requests\Messaging\SendMessageRequest;
use App\Http\Requests\Messaging\StartConversationRequest;
use App\Http\Resources\ConversationResource;
use App\Http\Resources\MessageResource;
use App\Models\Conversation;
use App\Services\MessagingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessagingController extends Controller
{
    public function index(Request $request, MessagingService $messagingService): JsonResponse
    {
        $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);
        $items = $messagingService->getUserInbox($request->integer('user_id'));

        return $this->successResponse(ConversationResource::collection($items), 'Boîte de réception récupérée avec succès.');
    }

    public function startConversation(StartConversationRequest $request, MessagingService $messagingService): JsonResponse
    {
        $conversation = $messagingService->startConversation($request->validated()['participant_ids']);

        return $this->successResponse(new ConversationResource($conversation), 'Conversation créée avec succès.', 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        return $this->successResponse(
            new ConversationResource($conversation->load(['participants', 'messages.sender', 'messages.replyTo'])),
            'Détail de la conversation récupéré avec succès.'
        );
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $this->authorize('sendMessage', $conversation);
        $data = $request->validated();

        return $this->successResponse(
            new MessageResource($messagingService->sendMessage($conversation, $data['sender_id'], $data)),
            'Message envoyé avec succès.',
            201
        );
    }

    public function markAsRead(MarkConversationAsReadRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $this->authorize('markAsRead', $conversation);
        $messagingService->markAsRead($conversation, $request->integer('user_id'));

        return $this->successResponse(null, 'Conversation marquée comme lue.');
    }
}
