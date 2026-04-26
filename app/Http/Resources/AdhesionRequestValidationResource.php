<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdhesionRequestValidationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'level'      => $this->level,
            'decision'   => $this->decision,
            'motif'      => $this->motif,
            'decided_at' => $this->decided_at,
            'validator'  => new UserResource($this->whenLoaded('validator')),
        ];
    }
}
