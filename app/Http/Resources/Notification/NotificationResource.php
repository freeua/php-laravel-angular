<?php

namespace App\Http\Resources\Notification;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NotificationResource
 *
 * @package App\Http\Resources\Notification
 * @mixin Collection
 */
class NotificationResource extends JsonResource
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
        /** @var $Notification */
        return [
            'id' => $this->id,
            'from'  => $this->data['from'],
            'subject'  => $this->data['subject'],
            'body' => $this->data['body'],
            'type' => $this->type(),
            'created' => $this->created_at,
            'readAt' => $this->read_at,
            'sender' => $this->sender(),
        ];
    }
}
