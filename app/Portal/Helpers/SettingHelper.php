<?php

namespace App\Portal\Helpers;

use App\Helpers\SettingHelper as BaseSettingsHelper;
use App\System\Models\Setting;

/**
 * Class SettingHelper
 *
 * @package App\Portal\Helpers
 */
class SettingHelper extends BaseSettingsHelper
{
    /**
     * @return string
     */
    public static function leasingablePdf()
    {
        return Setting::where('key', 'leasingable_pdf')->pluck('value')->first();
    }
}
