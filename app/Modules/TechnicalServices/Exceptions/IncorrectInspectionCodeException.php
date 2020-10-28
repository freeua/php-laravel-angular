<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class IncorrectInspectionCodeException extends ControlledException
{
    public function __construct()
    {
        $message = 'Incorrect inspection code';
        parent::__construct($message, 422, 'incorrectInspectionCode');
    }
}
