<?php

namespace App\System\Http\Resources;

use App\Portal\Models\FaqCategory;
use App\System\Models\Portal;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OfferSimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 * @mixin Collection
 */
class FaqCategoryResource extends JsonResource
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
        /** @var $this FaqCategory */
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'description'       => $this->description,
            'portal_id'         => $this->portal_id,
        ];
    }
}
