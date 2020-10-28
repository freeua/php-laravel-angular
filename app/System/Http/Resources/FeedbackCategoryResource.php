<?php

namespace App\System\Http\Resources;

use App\System\Models\FeedbackCategory;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class FeedbackCategoryResource
 *
 * @package App\System\Http\Resources
 */
class FeedbackCategoryResource extends JsonResource
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
        /** @var $this FeedbackCategory */
        return [
            'id'   => $this->id,
            'name' => $this->name
        ];
    }
}
