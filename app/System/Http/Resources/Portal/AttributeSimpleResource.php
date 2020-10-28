<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\Attribute;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class Simple
 *
 * @package App\System\Http\Resources\Portal
 */
class AttributeSimpleResource extends JsonResource
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
        /** @var $this Attribute */
        return [
            'id'    => $this->id,
            'slug'  => $this->slug,
            'name'  => $this->name,
            'value' => $this->value,
        ];
    }
}
