<?php

namespace App\Portal\Http\Resources\V1\Collections;

use App\Portal\Http\Resources\V1\LeasingSettingResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class SettingCollection
 *
 * @package App\Portal\Http\Resources\V1\Collections
 */
class SettingCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($value, $key) {
            return $key === 'leasing_settings' ? LeasingSettingResource::collection($value) : $value;
        })->toArray();
    }
}
