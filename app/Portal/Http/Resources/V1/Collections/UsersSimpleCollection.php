<?php

namespace App\Portal\Http\Resources\V1\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class UsersSimpleCollection
 *
 * @package App\Portal\Http\Resources\V1\Collections
 */
class UsersSimpleCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($user) {
            return [
                'id'   => $user->id,
                'name' => $user->name
            ];
        })->toArray();
    }
}
