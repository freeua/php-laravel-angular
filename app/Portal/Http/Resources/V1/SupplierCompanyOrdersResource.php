<?php

namespace App\Portal\Http\Resources\V1;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class SupplierCompanyOrdersResource extends JsonResource
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
        return [
            'id'  => $this->id,
            'name'  => $this->name,
            'total' => $this->total,
        ];
    }
}
