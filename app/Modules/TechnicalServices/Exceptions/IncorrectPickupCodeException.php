<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class IncorrectPickupCodeException extends ControlledException
{
    public function __construct()
    {
        $message = 'Incorrect pick up code';
        parent::__construct($message, 422, 'incorrectPickupCode');
    }
}
