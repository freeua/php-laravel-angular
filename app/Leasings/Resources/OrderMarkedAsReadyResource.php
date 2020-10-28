<?php

namespace App\Leasings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderMarkedAsReadyResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => StatusResource::make($this->status),
        ];
    }
}
