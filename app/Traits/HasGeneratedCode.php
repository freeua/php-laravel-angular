<?php
/**
 * Created by PhpStorm.
 * User: jpicornell
 * Date: 01/11/2018
 * Time: 13:10
 */

namespace App\Traits;

trait HasGeneratedCode
{
    /**
     * @param string $name
     * @param int    $lettersCount
     * @param int    $digitsCount
     * @param string $delimiter
     * @param string $prefix
     * @param string $field
     * @param int    $id
     *
     * @return string
     */
    public function generateCode(
        int $id,
        int $digitsCount = 3,
        string $delimiter = '',
        string $prefix = ''
    ): string {
        $code = $prefix . $delimiter;

        $code .= str_pad($id, $digitsCount, '0', STR_PAD_LEFT);

        return $code;
    }
}
