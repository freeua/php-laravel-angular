<?php
/**
 * Created by PhpStorm.
 * User: Ivan de la Fuente
 * Date: 04/11/2018
 * Time: 16:04
 */

namespace App\Helpers;

class FileSizeHelper
{

    /**
     * @param int $bytes
     * @param int $decimals
     * @return String
     */
    public static function getHumanFileSize(int $bytes, int $decimals = 0): String
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }
}
