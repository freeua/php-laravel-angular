<?php

namespace App\System\Http\Resources;

use App\Portal\Models\Contract;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class ContractSimpleResource
 *
 * @package App\System\Http\Resources
 * @mixin Collection
 */
class ContractSimpleResource extends JsonResource
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
        /** @var $this Contract */
        return [
            'id'     => $this->id,
            'number' => $this->number,
        ];
    }
}
