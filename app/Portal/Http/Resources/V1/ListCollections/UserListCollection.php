<?php

namespace App\Portal\Http\Resources\V1\ListCollections;

use App\Http\Resources\BaseListCollection;
use Illuminate\Support\Collection;

/**
 * Class UserListCollection
 *
 * @package App\Portal\Http\Resources\V1\ListCollections
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
            $roles = [];
            foreach ($user->roles as $role) {
                $roles[] = $role->name;
            }
            return [
                'id'   => $user->id,
                'code' => $user->code,
                'name' => $user->name,
                'email' => $user->email,
                'company' => $user->company,
                'role' => $user->role,
                'roles' => $roles,
                'status' => $user->status,
                'status_id' => $user->status_id,
                'active_contracts' => $user->active_contracts,
                'remaining_sign_contracts' => $user->remaining_sign_contracts
            ];
        });
    }
}
