<?php

namespace App\Portal\Http\Resources\V1;

use App\Models\Companies\Company;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanySimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class CompanySimpleResource extends JsonResource
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
        /** @var $this Company */
        return [
            'id'   => $this->id,
            'name' => $this->name,
            'parent_id' => $this->parent_id
        ];
    }
}
