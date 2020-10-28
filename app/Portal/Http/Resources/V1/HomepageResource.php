<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 20.03.2019
 * Time: 11:32
 */

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Homepage;
use Illuminate\Http\Resources\Json\JsonResource;

class HomepageResource extends JsonResource
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
        /** @var $this Homepage */
        return [
            'items' => $this->items,
        ];
    }
}
