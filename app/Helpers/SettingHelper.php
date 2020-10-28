<?php

namespace App\Helpers;

/**
 * Class SettingHelper
 *
 * @package App\Helpers
 */
class SettingHelper
{
    /**
     * @return string
     */
    public static function domain(): string
    {
        return self::get('domain');
    }

    /**
     * @return string
     */
    public static function logo(): string
    {
        return self::get('logo');
    }

    /**
     * @return string
     */
    public static function leasingablePdf()
    {
        return self::get('leasingable_pdf');
    }

    /**
     * @return null|string
     */
    public static function tz(): ?string
    {
        return self::get('timezone');
    }

    /**
     * @return null|string
     */
    public static function dateFormat(): ?string
    {
        return self::get('date_format');
    }

    /**
     * @return null|string
     */
    public static function timeFormat(): ?string
    {
        return self::get('time_format');
    }

    /**
     * @return string
     */
    public static function datetimeFormat(): string
    {
        return self::dateFormat() . ' ' . self::timeFormat();
    }

    /**
     * @return string
     */
    public static function color(): string
    {
        return self::get('color');
    }

    /**
     * @return string
     */
    public static function email(): string
    {
        return self::get('email');
    }

    /**
     * Returns setting by name
     *
     * @param string $name
     *
     * @return string
     */
    public static function get(string $name): ?string
    {
        return config(config('app.settings_key') . '.' . $name);
    }
}
