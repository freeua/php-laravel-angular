<?php

namespace App\Leasings\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderPickedUpResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'creditNote' => $this->creditNoteFile ? "/leasings/orders/{$this->id}/credit-note" : null,
            'status' => StatusResource::make($this->status),
        ];
    }
}
