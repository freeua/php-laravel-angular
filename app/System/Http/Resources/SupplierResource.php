<?php

namespace App\System\Http\Resources;

use App\Helpers\DateHelper;
use App\Portal\Models\Supplier;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class SupplierResource
 *
 * @package App\System\Http\Resources
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
        /** @var $this Supplier */
        return [
            'id'                    => $this->id,
            'code'                  => $this->code,
            'name'                  => $this->name,
            'admin_first_name'      => $this->admin_first_name,
            'admin_last_name'       => $this->admin_last_name,
            'admin_email'           => $this->admin_email,
            'vat'                   => $this->vat,
            'zip'                   => $this->zip,
            'city_id'               => $this->city_id,
            'city'                  => $this->city,
            'portals'               => $this->portals,
            'address'               => $this->address,
            'phone'                 => $this->phone,
            'status'                => $this->status,
            'products_count'        => $this->products_count,
            'employees_count'       => $this->users()->count(),
            'active_from'           => DateHelper::date($this->created_at),
            'gp_number'             => $this->gp_number,
            'bank_account'          => $this->bank_account,
            'bank_name'             => $this->bank_name,
            'grefo'                 => $this->grefo,
        ];
    }
}
