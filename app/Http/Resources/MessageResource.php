<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'sender'     => new UserResource($this->whenLoaded('sender')),
            'text'       => $this->text,
            'type'       => $this->type,
            'image_url'  => $this->image_url,
            'file_url'   => $this->file_url,
            'reply_to'   => new MessageResource($this->whenLoaded('replyTo')),
            'status'     => $this->status,
            'deleted_at' => $this->deleted_at,
            'created_at' => $this->created_at,
        ];
    }
}
