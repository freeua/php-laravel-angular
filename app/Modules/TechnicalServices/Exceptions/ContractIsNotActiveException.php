<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class ContractIsNotActiveException extends ControlledException
{
    public function __construct()
    {
        $message = 'Contract is not active';
        parent::__construct($message, 422, 'contractInactive');
    }
}
