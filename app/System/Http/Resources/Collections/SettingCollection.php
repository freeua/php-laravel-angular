<?php

namespace App\System\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use App\Helpers\TextHelper;
use App\Models\Text;

/**
 * Class SettingCollection
 *
 * @package App\System\Http\Resources\Collections
 */
class SettingCollection extends ResourceCollection
{
    protected $texts;

    public function texts($texts)
    {
        $this->texts = $texts;
        return $this;
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $settings = $this->collection->pluck('value', 'key')->toArray();
        $settings['mainDomain'] = env('APP_URL_BASE');
        $settings['mainDomain'] = env('APP_URL_BASE');
        $settings['texts'] = $this->getTextSettingsCollection();
        return $settings;
    }

    private function getTextSettingsCollection()
    {
        $textCollection = \Cache::rememberForever(Text::getCacheCollectionKey(), function () {
            return TextHelper::getSettingsCollection($this->texts);
        });
        return $textCollection;
    }
}
