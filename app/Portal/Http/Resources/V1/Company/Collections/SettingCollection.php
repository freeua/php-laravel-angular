<?php

namespace App\Portal\Http\Resources\V1\Company\Collections;

use App\Portal\Http\Resources\V1\CompanyLeasingSettingResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class SettingCollection
 *
 * @package App\Portal\Http\Resources\V1\Company\Collections
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
            return $key === 'leasing_settings' ? CompanyLeasingSettingResource::collection($value) : $value;
        })->toArray();
    }
}
