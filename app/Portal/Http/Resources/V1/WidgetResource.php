<?php

namespace App\Portal\Http\Resources\V1;

use App\Portal\Models\Widget;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class WidgetResource
 *
 * @package App\Portal\Http\Resources
 */
class WidgetResource extends JsonResource
{
    /** @var int */
    private $withData = true;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        /**  @var $this Widget */
        $result = [
            'id'       => $this->id,
            'source'   => $this->source,
            'style'    => $this->style,
            'position' => $this->position,
        ];

        if ($this->withData) {
            /**  @var $this Widget */
            foreach ($this->data as $items) {
                $items->transform(function ($item, $key) {
                    if (!empty($item['date'])) {
                        // ToDo format date
                        $item['date'] = Carbon::createFromFormat('Y-m-d', $item['date'])->toDateString();
                    }
                    return $item;
                });
            }
            $result['data'] = $this->data;
        }

        return $result;
    }

    /**
     * @return WidgetResource
     */
    public function withoutData(): WidgetResource
    {
        $this->withData = false;

        return $this;
    }
}
