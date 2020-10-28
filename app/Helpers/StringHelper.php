<?php

namespace App\Helpers;

/**
 * Class StringHelper
 *
 * @package App\Helpers
 */
abstract class StringHelper
{
    /**
     * Generate random string with non-word characters
     *
     * @param int $length
     *
     * @param int $nonWordCharactersMax
     *
     * @return string
     */
    public static function password(int $length = 10, int $nonWordCharactersMax = 2): string
    {
        if ($length <= 2) {
            return str_random($length);
        }

        $nonWordCharacters = "./\\()\"':,.;<>~!@#$%^&*|+=[]{}`~?-";

        $nonWordCharactersCount = rand(1, $nonWordCharactersMax);

        $str = str_random($length - $nonWordCharactersCount) . substr(str_shuffle($nonWordCharacters), rand(1, 15), $nonWordCharactersCount);

        return substr($str, 0, 1) . str_shuffle(substr($str, 1));
    }
}
