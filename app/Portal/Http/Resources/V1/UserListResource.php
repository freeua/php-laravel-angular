<?php

namespace App\Portal\Http\Resources\V1;

use App\Helpers\DateHelper;
use App\Models\Permission;
use App\Portal\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserListResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class UserListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($user)
    {
        /** @var $this User */
        return [
            'id'              => $this->id,
            'code'            => $this->code,
            'email'           => $this->email,
            'role'            => $this->role,
            'created_at'      => $this->created_at,
            'name'            => $this->name,
            'status_id'       => $this->status_id,
            'status'          => $this->status,
            'additional_info' => $this->additional_info,
            'company_name'    => $this->company_name,
            'company_code'    => $this->company_code,
            'company'         => CompanyResource::make($this->company),
        ];
    }
}
