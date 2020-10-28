<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        /** @var $this \App\Models\Permission */
        return [
            'name'=> $this->name,
            'label' => $this->label,
            'guard_name' => $this->guard_name,
            'id' => $this->id,
        ];
    }
}
