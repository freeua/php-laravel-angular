<?php
namespace App\ExternalLogin\Exceptions;

use App\Exceptions\ControlledException;

class WrongRoleError extends ControlledException
{
    public function __construct()
    {
        parent::__construct("User has not the right role", 422, "wrongRoleError");
    }
}
