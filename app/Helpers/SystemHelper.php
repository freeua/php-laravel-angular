<?php

namespace App\Helpers;

/**
 * Class SystemHelper
 *
 * @package App\Helpers
 */
class SystemHelper
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function frontendUrl(string $path = ''): string
    {
        $url = config('app.system_admin_url');

        return $path ? $url . '/' . $path : $url;
    }
}
