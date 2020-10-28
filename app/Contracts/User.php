<?php

namespace App\Contracts;

/**
 * Interface User
 *
 * @package App\Contracts
 */
interface User
{
    /**
     * @return string
     */
    public function getDefaultUserFolder(): string;
}
