<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messaging\MarkConversationAsReadRequest;
use App\Http\Requests\Messaging\SendMessageRequest;
use App\Http\Requests\Messaging\StartConversationRequest;
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

        return response()->json(['message' => 'Boîte de réception utilisateur.', 'data' => $items]);
    }

    public function startConversation(StartConversationRequest $request, MessagingService $messagingService): JsonResponse
    {
        $conversation = $messagingService->startConversation($request->validated()['participant_ids']);

        return response()->json(['message' => 'Conversation créée avec succès.', 'data' => $conversation], 201);
    }

    public function show(Conversation $conversation): JsonResponse
    {
        $conversation->load(['participants', 'messages.sender', 'messages.replyTo']);

        return response()->json(['message' => 'Détail de la conversation.', 'data' => $conversation]);
    }

    public function sendMessage(SendMessageRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $data = $request->validated();
        $message = $messagingService->sendMessage($conversation, $data['sender_id'], $data);

        return response()->json(['message' => 'Message envoyé avec succès.', 'data' => $message], 201);
    }

    public function markAsRead(MarkConversationAsReadRequest $request, Conversation $conversation, MessagingService $messagingService): JsonResponse
    {
        $messagingService->markAsRead($conversation, $request->integer('user_id'));

        return response()->json(['message' => 'Conversation marquée comme lue.']);
    }
}
