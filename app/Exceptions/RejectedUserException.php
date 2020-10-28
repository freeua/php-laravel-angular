<?php

namespace App\Exceptions;

class RejectedUserException extends ControlledException
{
    public function __construct()
    {
        $message = 'Dieser Mitarbeiter wurde abgelehnt';
        parent::__construct($message, 400, 'rejectedUser');
    }
}
