<?php

namespace App\Http\Resources\Notification\ListCollection;

use App\Models\Notification;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class NotificationListCollection
 *
 * @package App\Http\Resources\V1\Notification\ListCollection\NotificationListCollection
 */
class NotificationListCollection extends JsonResource
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
        /** @var $this Notification */
        return [
            'id'           => $this->id,
            'from'         => $this->data['from'],
            'subject'      => $this->data['subject'],
            'body'         => $this->data['body'],
            'created'      => $this->created_at,
            'readAt'       => $this->read_at,
            'status'       => $this->getStatus()
        ];
    }
}
