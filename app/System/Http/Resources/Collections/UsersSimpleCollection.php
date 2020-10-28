<?php

namespace App\System\Http\Resources\Collections;

use App\System\Models\User;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class UsersSimpleCollection
 *
 * @package App\System\Http\Resources\Collections
 */
class UsersSimpleCollection extends ResourceCollection
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return $this->collection->transform(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->first_name . ' ' . $user->last_name,
                'type' => $user->portal_id || $user->supplier_id || $user->company_id  ? 'portal' : 'system',
            ];
        })->toArray();
    }
}
