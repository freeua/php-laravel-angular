<?php

namespace App\Exceptions;

class MissingContractFieldsException extends ControlledException
{
    public function __construct($user)
    {
        \Log::warning('User don\'t have specified all contract fields');
        parent::__construct('Missing some contract field', 422, 'missingContractfields');
    }
}
