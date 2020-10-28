<?php

namespace App\Helpers;

use Carbon\Carbon;

/**
 * Class DateHelper
 *
 * @package App\Helpers
 */
class DateHelper
{
    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function date(Carbon $date, bool $tz = true): string
    {
        $format = SettingHelper::dateFormat() ?? self::defaultDateFormat();

        return $tz ? self::tz($date)->format($format) : $date->format($format);
    }

    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function time(Carbon $date, bool $tz = true): string
    {
        return $tz ? self::tz($date)->format(SettingHelper::timeFormat()) : $date->format(SettingHelper::timeFormat());
    }

    /**
     * @param Carbon $date
     * @param bool   $tz
     *
     * @return string
     */
    public static function dt(Carbon $date, bool $tz = true): string
    {
        return $tz ? self::tz($date)->format(SettingHelper::datetimeFormat()) : $date->format(SettingHelper::datetimeFormat());
    }

    /**
     * @param Carbon $date
     *
     * @return Carbon
     */
    public static function tz(Carbon $date): Carbon
    {
        $tz = SettingHelper::tz() ?? self::defaultTZ();

        return $date->setTimezone($tz);
    }

    /**
     * @return string
     */
    private static function defaultDateFormat(): string
    {
        return config('app.date_format');
    }

    /**
     * @return string
     */
    public static function dateFormat(): string
    {
        return SettingHelper::dateFormat() ?? self::defaultDateFormat();
    }

    /**
     * @return string
     */
    private static function defaultTZ(): string
    {
        return config('app.timezone');
    }
}
