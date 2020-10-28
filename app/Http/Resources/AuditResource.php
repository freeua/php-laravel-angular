<?php
namespace App\Http\Resources;

use App\Models\Audit;
use Illuminate\Http\Resources\Json\JsonResource;

class AuditResource extends JsonResource
{
    public function toArray($request)
    {
        /** @var $this Audit */
        return [
            'description' => $this->description,
            'createdAt' => $this->created_at,
            'user' => $this->user ? $this->user->fullName : ($this->partner ? $this->partner->name : null),
        ];
    }
}
