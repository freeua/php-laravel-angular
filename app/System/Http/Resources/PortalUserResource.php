<?php

namespace App\System\Http\Resources;

use App\Http\Resources\Portals\PortalResource;
use App\Models\Permission;
use App\Portal\Models\User as PortalUser;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class PortalUserResource
 *
 * @package App\System\Http\Resources
 */
class PortalUserResource extends JsonResource
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
        /** @var $this PortalUser */
        return [
            'id' => $this->id,
            'code' => $this->code,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'status' => $this->status,
            'portal_id' => $this->portal_id,
            'portal' => new PortalResource($this->whenLoaded('portal')),
            'hasEditPermission' => $this->hasPermissionTo(Permission::EDIT_PORTAL_DATA, 'portal'),
        ];
    }
}
