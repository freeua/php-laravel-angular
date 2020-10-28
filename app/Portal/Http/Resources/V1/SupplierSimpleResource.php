<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierSimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class SupplierSimpleResource extends JsonResource
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
        /** @var $this Supplier */
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }
}
