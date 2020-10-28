<?php

namespace App\Http\Resources\Notification;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SenderResource
 *
 * @package App\Http\Resources\V1\Notification
 */
class SenderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this User */
        return [
            'id' => $this->id,
            'name' => $this->fullName,
            'type' => $this->system ? Notification::SYSTEM : Notification::PORTAL,
        ];
    }
}
