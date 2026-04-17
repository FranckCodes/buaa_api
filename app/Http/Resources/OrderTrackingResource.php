<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderTrackingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'label'     => $this->label,
            'done'      => (bool) $this->done,
            'date_done' => $this->date_done,
            'ordre'     => $this->ordre,
        ];
    }
}
