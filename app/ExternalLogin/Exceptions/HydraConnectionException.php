<?php
namespace App\ExternalLogin\Exceptions;

use App\Exceptions\ControlledException;

class HydraConnectionException extends ControlledException
{
    public function __construct(\Exception $exception)
    {
        parent::__construct("Ein unerwarteter Fehler ist aufgetreten. Unser technisches Team wurde benachrichtigt. " . $exception->getMessage(), 500, "hydraConnectException");
    }
}
