<?php
namespace App\Documents\Exceptions;

use App\Exceptions\ControlledException;

class WrongUserProfileException extends ControlledException
{
    public function __construct()
    {
        $message = 'User has not a correctly configured role';
        parent::__construct($message, 403, 'wrongUserProfile');
    }
}
