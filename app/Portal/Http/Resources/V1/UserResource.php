<?php

namespace App\Portal\Http\Resources\V1;

use App\Helpers\DateHelper;
use App\Modules\TechnicalServices\Resources\TechnicalServiceListCollection;
use App\Modules\TechnicalServices\Resources\TechnicalServiceResource;
use App\Models\Permission;
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
            'code' => $this->code,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'fullName' => $this->fullName,
            'email' => $this->email,
            'company_id' => $this->company_id,
            'portalId' => $this->portal_id,
            'company' => new CompanyResource($this->whenLoaded('company')),
            'technicalServices' => new TechnicalServiceResource($this->whenLoaded('technicalServices')),
            'supplier_id' => $this->supplier_id,
            'supplier' => new SupplierSimpleResource($this->whenLoaded('supplier')),
            'street' => $this->street,
            'city_id' => $this->city_id,
            'country' => $this->country,
            'employee_number' => $this->employee_number,
            'phone' => $this->phone,
            'postal_code' => $this->postal_code,
            'salutation' => $this->salutation,
            'role' => $this->getRoleName(),
            'roles' => $this->getRoleNames(),
            'permissions' => $this->getAllPermissions(),
            'active_contracts' => $this->active_contracts,
            'status' => $this->status,
            'active_from' => DateHelper::date($this->created_at),
            'policy_checked' => $this->policy_checked,
            'hasEditPermission' => $this->hasPermissionTo(Permission::EDIT_PORTAL_DATA, 'portal'),
        ];
    }
}
