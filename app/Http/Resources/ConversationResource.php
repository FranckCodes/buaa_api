<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConversationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'last_message_at' => $this->last_message_at,
            'participants'    => UserResource::collection($this->whenLoaded('participants')),
            'messages'        => MessageResource::collection($this->whenLoaded('messages')),
            'created_at'      => $this->created_at,
        ];
    }
}
