<?php

namespace App\Http\Resources\Cms\ListCollection;

use App\Models\Text;
use App\Http\Resources\BaseListCollection;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Collection;
use App\Helpers\PortalHelper;
use App\Helpers\TextHelper;

/**
 * Class TextListCollection
 *
 * @package App\Http\Resources\Cms\ListCollection\TextListCollection
 */
class TextListCollection extends ResourceCollection
{
    /**
     * Specifies data item in response
     *
     * @return Collection
     */
    public function toArray($request): Array
    {
        if (!PortalHelper::id()) {
            $result = array(
                'default' => TextHelper::getSystemCollection($this->collection)
            );
        } else {
            $result = array(
                'default' => TextHelper::getPortalCollection($this->collection)
            );
        }
        return $result;
    }
}
