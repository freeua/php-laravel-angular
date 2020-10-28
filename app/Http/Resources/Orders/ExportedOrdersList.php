<?php

namespace App\Http\Resources\Orders;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\Resource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ExportedOrdersList extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        Resource::withoutWrapping();
        return [
            'count' => $this->collection->count(),
            'orders' => ExportedOrder::collection($this->collection),
            'time' => Carbon::now()->timestamp,
        ];
    }
}
