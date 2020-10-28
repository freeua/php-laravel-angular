<?php

namespace App\Partners\Resources;

use App\Portal\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class UserResource
 *
 * @package App\Portal\Http\Resources\V1
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
            'id' => $this->id,
            'sub' => $this->id,
            'code' => $this->code,
            'given_name' => $this->first_name,
            'family_name' => $this->last_name,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'companyId' => $this->company_id,
            'companyName' => $this->company->name,
            'address' => $this->street,
            'city' => isset($this->city) ? $this->city->name : null,
            'country' => $this->country,
            'phone' => $this->phone,
            'postalCode' => $this->postal_code,
            'salutation' => $this->salutation,
            'status' => $this->status->label,
            'minBikePrice' => $this->minBikePrice,
            'maxBikePrice' => $this->maxBikePrice,
            'maxNumberContracts' => $this->maxNumberContracts,
        ];
    }
}
