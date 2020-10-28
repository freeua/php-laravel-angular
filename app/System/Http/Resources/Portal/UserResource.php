<?php

namespace App\System\Http\Resources\Portal;

use App\Portal\Models\User;
use App\Helpers\DateHelper;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @package App\System\Http\Resources\Portal
 */
class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this User */
        return [
            'id'               => $this->id,
            'first_name'       => $this->first_name,
            'last_name'        => $this->last_name,
            'email'            => $this->email,
            'avatar'           => $this->avatar,
            'active_contracts' => $this->active_contracts,
            'status'           => $this->status,
            'company'          => new CompanyResource($this->company),
            'active_from'      => DateHelper::date($this->created_at),
        ];
    }
}
