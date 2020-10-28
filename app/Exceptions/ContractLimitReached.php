<?php
namespace App\Exceptions;

class ContractLimitReached extends ControlledException
{
    public function __construct()
    {
        $message = 'User has reached the maximum amount of contracts';
        parent::__construct($message, 422, 'contractLimitReached');
    }
}
