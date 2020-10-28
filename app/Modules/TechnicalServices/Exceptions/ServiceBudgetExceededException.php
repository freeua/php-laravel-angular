<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class ServiceBudgetExceededException extends ControlledException
{
    public function __construct()
    {
        $message = 'The service amount exceeds the service budget';
        parent::__construct($message, 422, 'serviceBudgetExceeded');
    }
}
