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
        return $this->paginatedResponse(
            $messagingService->getUserInbox($request->user()->id),
            'Conversations récupérées avec succès.',
            fn ($conversation) => new ConversationResource($conversation)
        );
    }

    public function startConversation(StartConversationRequest $request, MessagingService $messagingService): JsonResponse
    {
        $this->authorize('create', Conversation::class);

        $conversation = $messagingService->startConversation($request->validated()['participant_ids']);

        return $this->successResponse(new ConversationResource($conversation), 'Conversation créée avec succès.', 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $this->authorize('view', $conversation);

        $conversation->load([
            'participants',
            'participantRows',
            'messages.sender',
            'messages.replyTo',
        ]);

        return $this->successResponse(new ConversationResource($conversation), 'Détail de la conversation récupéré avec succès.');
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $this->authorize('sendMessage', $conversation);

        $data    = $request->validated();
        $message = $messagingService->sendMessage($conversation, $data['sender_id'], $data);

        return $this->successResponse(new MessageResource($message), 'Message envoyé avec succès.', 201);
    }

    public function markAsRead(MarkConversationAsReadRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $this->authorize('markAsRead', $conversation);

        $messagingService->markAsRead($conversation, $request->string('user_id')->toString());

        return $this->successResponse(null, 'Conversation marquée comme lue.');
    }
}
