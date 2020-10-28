<?php

namespace App\System\Http\Resources\ListCollections;

use App\Http\Resources\BaseListCollection;
use App\Models\Portal;
use Illuminate\Support\Collection;

/**
 * Class PortalListCollection
 *
 * @package App\System\Http\Resources\ListCollections
 */
class PortalListCollection extends BaseListCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    protected function data(): Collection
    {
        return $this->collection->transform(function ($portal) {
            /** @var $portal Portal */
            return [
                'id'          => $portal->id,
                'name'        => $portal->name,
                'code'        => $portal->code,
                'domain'      => $portal->domain,
                'company_vat' => $portal->company_vat,
                'status'      => $portal->status
            ];
        });
    }
}
