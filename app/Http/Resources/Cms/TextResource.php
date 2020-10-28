<?php

namespace App\Http\Resources\Cms;

use App\Helpers\DateHelper;
use App\Models\Text;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class TextResource
 *
 * @package App\Http\Resources\Cms
 * @mixin Collection
 */
class TextResource extends JsonResource
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
        /** @var $Text */
        return [
            'id' => $this->id,
            'portalId' => $this->portal_id,
            'key' => $this->data['key'],
            'description' => $this->data['description'],
            'title' => $this->data['title'],
            'subtitle' => $this->data['subtitle'],
        ];
    }
}
