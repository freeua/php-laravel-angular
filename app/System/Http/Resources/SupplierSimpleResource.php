<?php

namespace App\System\Http\Resources;

use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierSimpleResource
 *
 * @package App\System\Http\Resources
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
