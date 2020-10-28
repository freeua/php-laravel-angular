<?php

namespace App\Portal\Http\Resources\V1\Employee\Collections;

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
        return $this->collection->toArray();
    }
}
