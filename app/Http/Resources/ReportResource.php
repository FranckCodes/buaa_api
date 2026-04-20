<?php

namespace App\Http\Resources;

use App\Http\Resources\Reference\ReferenceValueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'client'       => new ClientResource($this->whenLoaded('client')),
            'superviseur'  => new UserResource($this->whenLoaded('superviseur')),
            'type'         => new ReferenceValueResource($this->whenLoaded('type')),
            'status'       => new ReferenceValueResource($this->whenLoaded('status')),
            'summary'      => $this->summary,
            'value_numeric' => $this->value_numeric,
            'value_unit'   => $this->value_unit,
            'value_text'   => $this->value_text,
            'details'      => $this->details,
            'date_rapport' => $this->date_rapport,
            'validated_by' => new UserResource($this->whenLoaded('validatedBy')),
            'motif_rejet'  => $this->motif_rejet,
            'documents'    => DocumentResource::collection($this->whenLoaded('documents')),
            'created_at'   => $this->created_at,
        ];
    }
}
