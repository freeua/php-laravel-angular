<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class IncorrectFrameNumberException extends ControlledException
{
    public function __construct()
    {
        $message = 'Incorrect frame number';
        parent::__construct($message, 422, 'incorrectFrameNumber');
    }
}
