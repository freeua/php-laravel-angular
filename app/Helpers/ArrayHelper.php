<?php
namespace App\Helpers;

/**
 * Class ArrayHelper
 *
 * @package App\Helpers
 */
abstract class ArrayHelper
{
    /**
     * Expands arrays with keys that have dot notation
     *
     * @param array $array
     *
     * @return array
     */
    public static function dotToMulti(array $array) : array
    {
        $result = [];

        foreach ($array as $key => $value) {
            array_set($result, $key, $value);
        }

        return $result;
    }
}
