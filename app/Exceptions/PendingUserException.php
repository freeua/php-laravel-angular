<?php

namespace App\Exceptions;

class PendingUserException extends ControlledException
{
    public function __construct()
    {
        $message = 'Dieser Mitarbeiter steht noch aus';
        parent::__construct($message, 400, 'pendingUser');
    }
}
