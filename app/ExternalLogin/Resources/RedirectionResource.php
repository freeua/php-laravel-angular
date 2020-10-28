<?php
namespace App\ExternalLogin\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RedirectionResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'redirectTo' => $this->redirect_to,
        ];
    }
}
