<?php
namespace App\Exceptions;

use App\Exceptions\ControlledException;

class WrongPickupCode extends ControlledException
{
    public function __construct()
    {
        parent::__construct("Wrong pick up code", 422, "wrongPickUpCode");
    }
}
