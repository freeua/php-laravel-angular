<?php

namespace App\System\Http\Resources\Portal;

use App\Models\Companies\Company;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanyResource
 *
 * @package App\System\Http\Resources\Portal
 */
class CompanyResource extends JsonResource
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
        /** @var $this Company */
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'address'        => $this->address,
            'city'           => $this->city,
            'code'           => $this->code,
            'leasing_factor' => $this->leasing_factor,
            'env_settings'   => $this->env_settings,
            'cycles'         => $this->cycles,
            'is_active'      => $this->is_active,
        ];
    }
}
