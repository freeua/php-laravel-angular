<?php

namespace App\Exceptions;

class ContractNotFoundException extends ControlledException
{
    public function __construct()
    {
        $message = 'Contract not found';
        parent::__construct($message, 404, 'contractNotFound');
    }
}
