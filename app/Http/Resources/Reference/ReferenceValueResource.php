<?php

namespace App\Http\Resources\Reference;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReferenceValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'code'        => $this->code,
            'label'       => $this->label,
            'description' => $this->description,
            'is_active'   => (bool) $this->is_active,
            'sort_order'  => $this->sort_order,
        ];
    }
}
