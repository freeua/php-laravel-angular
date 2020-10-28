<?php


namespace App\Webhooks\Exceptions;

use App\Exceptions\ControlledException;
use Throwable;

class RecipientInvalid extends \Exception
{
    public function __construct($recipient)
    {
        parent::__construct("Recipient {$recipient} is not a valid email", 'recipientInvalid');
    }
}
