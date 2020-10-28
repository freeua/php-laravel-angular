<?php

namespace App\Libraries\Pdf\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Pdf
 *
 * @package App\Libraries\Pdf\Facades
 */
class Pdf extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'pdf';
    }
}
