<?php

namespace App\Portal\Http\Resources\V1\Company;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Resources\V1\CityResource;
use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CompanySupplierResource
 * @package App\Portal\Http\Resources\V1\Company
 */
class CompanySupplierResource extends JsonResource
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
        /** @var $this Supplier */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'vat' => $this->vat,
            'admin_first_name' => $this->admin_first_name,
            'admin_last_name' => $this->admin_last_name,
            'admin_email' => $this->admin_email,
            'zip' => $this->zip,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'current_offers' => $this->offers()->where('company_id', AuthHelper::companyId())->count(),
            'current_orders' => $this->orders()->where('company_id', AuthHelper::companyId())->count(),
            'current_contracts' => $this->contracts()->where('company_id', AuthHelper::companyId())->count()
        ];
    }
}
