<?php
namespace App\ExternalLogin\Exceptions;

use App\Exceptions\ControlledException;

class UserInactiveError extends ControlledException
{
    public function __construct()
    {
        parent::__construct("User status is inactive", 422, "userInactiveError");
    }
}
