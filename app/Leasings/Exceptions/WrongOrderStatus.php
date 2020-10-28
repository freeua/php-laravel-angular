<?php
namespace App\Leasings\Exceptions;

use App\Exceptions\ControlledException;

class WrongOrderStatus extends ControlledException
{
    public function __construct()
    {
        parent::__construct('The order is not in the correct status', 422, "wrongStatusOrder");
    }
}
