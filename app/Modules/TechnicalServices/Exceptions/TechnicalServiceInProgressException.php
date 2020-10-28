<?php
namespace App\Modules\TechnicalServices\Exceptions;

use App\Exceptions\ControlledException;

class TechnicalServiceInProgressException extends ControlledException
{
    public function __construct()
    {
        $message = 'Contract has an active TechnicalService';
        parent::__construct($message, 422, 'technicalServiceInProgress');
    }
}
