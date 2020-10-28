<?php

namespace App\Portal\Http\Resources\V1\Company\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class UserListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections\Company
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
                'id'              => $user->id,
                'code'            => $user->code,
                'email'           => $user->email,
                'role'            => $user->role,
                'created_at'      => $user->created_at,
                'name'            => $user->name,
                'status_id'       => $user->status_id,
                'status'          => $user->status,
                'additional_info' => $user->additional_info,
                'company_name'    => $user->company_name
            ];
        });
    }
}
