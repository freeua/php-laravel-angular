<?php
namespace App\ExternalLogin\Exceptions;

use App\Exceptions\ControlledException;

class AuthenticationError extends ControlledException
{
    public function __construct($message)
    {
        parent::__construct($message, 401, "authenticationError");
    }
}
