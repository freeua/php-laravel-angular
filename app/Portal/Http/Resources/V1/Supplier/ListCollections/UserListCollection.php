<?php

namespace App\Portal\Http\Resources\V1\Supplier\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class UserListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections\Supplier
 */
class UserListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($user) {
            return [
                'id'     => $user->id,
                'code'   => $user->code,
                'email'  => $user->email,
                'name'   => $user->name,
                'status' => $user->status,
            ];
        });
    }
}
