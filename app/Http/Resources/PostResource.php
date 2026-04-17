<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'author'                => new UserResource($this->whenLoaded('author')),
            'content'               => $this->content,
            'tag'                   => new ReferenceValueResource($this->whenLoaded('tag')),
            'status'                => new ReferenceValueResource($this->whenLoaded('status')),
            'validated_by'          => new UserResource($this->whenLoaded('validatedBy')),
            'motif_rejet'           => $this->motif_rejet,
            'likes_count'           => $this->likes_count,
            'media'                 => PostMediaResource::collection($this->whenLoaded('media')),
            'comments'              => CommentResource::collection($this->whenLoaded('comments')),
            'liked_by_users_count'  => $this->whenCounted('likedByUsers'),
            'saved_by_users_count'  => $this->whenCounted('savedByUsers'),
            'created_at'            => $this->created_at,
            'updated_at'            => $this->updated_at,
        ];
    }
}
