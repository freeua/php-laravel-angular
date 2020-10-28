<?php

namespace App\System\Http\Resources;

use App\Helpers\DateHelper;
use App\System\Models\Widget;
use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class WidgetResource
 *
 * @package App\System\Http\Resources
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
                        $item['date'] = DateHelper::date(Carbon::createFromFormat('Y-m-d', $item['date']), false);
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
