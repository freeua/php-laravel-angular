<?php

namespace App\System\Http\Resources;

use App\Models\Permission;
use App\System\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @package App\System\Http\Resources
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this User */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->first_name . ' ' . $this->last_name,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'company' => $this->company ? $this->company->name : '',
            'address' => $this->address,
            'role' => $this->getRoleName(),
            'status' => $this->status,
            'type' => $this->portal_id || $this->supplier_id || $this->company_id ? 'portal' : 'system',

        ];
    }
}
