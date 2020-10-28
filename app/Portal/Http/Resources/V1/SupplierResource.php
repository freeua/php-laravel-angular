<?php

namespace App\Portal\Http\Resources\V1;

use App\Helpers\PortalHelper;
use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierResource
 *
 * @package App\Portal\Http\Resources\V1
 */
class SupplierResource extends JsonResource
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
        $portal = $this->portals()->where('portals.id', '=', PortalHelper::id())
            ->first();
        /** @var $this Supplier */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'logo' => $this->logo,
            'name' => $this->name,
            'vat' => $this->vat,
            'admin_first_name' => $this->admin_first_name,
            'admin_last_name' => $this->admin_last_name,
            'admin_email' => $this->admin_email,
            'zip' => $this->zip,
            'city_id' => $this->city_id,
            'city' => new CityResource($this->city),
            'companies' => CompanySimpleResource::collection($this->companies),
            'address' => $this->address,
            'phone' => $this->phone,
            'status' => $this->status,
            'pivot_status' => $portal ? $portal->pivot->status_id : $this->status->id,
            'blind_discount' => $portal ? $portal->pivot->blind_discount : 0,
            'active_from' => $this->getActiveFrom() ? $this->getActiveFrom()->toDateString() : null,
            'gp_number' => $this->gp_number,
            'bank_account' => $this->bank_account,
            'bank_name' => $this->bank_name,
            'grefo' => $this->grefo,
        ];
    }
}
