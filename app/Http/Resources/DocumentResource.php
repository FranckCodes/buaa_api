<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'type_document' => $this->type_document,
            'nom_fichier'   => $this->nom_fichier,
            'url'           => $this->url,
            'taille_bytes'  => $this->taille_bytes,
            'mime_type'     => $this->mime_type,
            'uploaded_by'   => new UserResource($this->whenLoaded('uploadedBy')),
            'created_at'    => $this->created_at,
        ];
    }
}
