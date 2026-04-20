<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'author'     => new UserResource($this->whenLoaded('author')),
            'text'       => $this->text,
            'created_at' => $this->created_at,
        ];
    }
}
