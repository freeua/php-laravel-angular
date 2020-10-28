<?php
namespace App\Exceptions;

class TokenExpiredError extends ControlledException
{
    public function __construct()
    {
        $message = 'Token has expired';
        parent::__construct($message, 401, 'tokenExpired');
    }
}
