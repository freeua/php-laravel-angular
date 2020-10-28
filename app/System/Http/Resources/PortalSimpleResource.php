<?php

namespace App\System\Http\Resources;

use App\Models\Portal;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PortalSimpleResource
 *
 * @package App\System\Http\Resources
 */
class PortalSimpleResource extends JsonResource
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
        /** @var $this Portal */
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }
}
