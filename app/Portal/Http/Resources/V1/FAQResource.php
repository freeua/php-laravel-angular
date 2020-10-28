<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Faq;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Class OfferSimpleResource
 *
 * @package App\Portal\Http\Resources\V1
 * @mixin Collection
 */
class FAQResource extends JsonResource
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
        /** @var $this FAQ */
        return [
            'id'                => $this->id,
            'question'          => $this->question,
            'answer'            => $this->answer,
            'company_id'        => $this->company_id,
            'company'           => new CompanySimpleResource($this->whenLoaded('company')),
            'author'            => $this->author,
            'user'              => new UserResource($this->whenLoaded('user')),
            'category_id'       => $this->category_id,
            'category'          => new UserResource($this->whenLoaded('user')),
        ];
    }
}
