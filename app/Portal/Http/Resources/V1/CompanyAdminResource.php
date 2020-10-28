<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this User */
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'permissions' => $this->permissions,
            'roles' => $this->getRoleNames(),
        ];
    }
}
