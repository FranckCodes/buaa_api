<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'category'     => $this->category,
            'type'         => $this->type,
            'title'        => $this->title,
            'body'         => $this->body,
            'is_read'      => (bool) $this->is_read,
            'action_label' => $this->action_label,
            'action_url'   => $this->action_url,
            'from_user'    => new UserResource($this->whenLoaded('fromUser')),
            'created_at'   => $this->created_at,
        ];
    }
}
