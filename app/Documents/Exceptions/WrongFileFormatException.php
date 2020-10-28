<?php
namespace App\Documents\Exceptions;

use App\Exceptions\ControlledException;

class WrongFileFormatException extends ControlledException
{
    public function __construct()
    {
        $message = 'Wrong file format';
        parent::__construct($message, 422, 'wrongFileFormat');
    }
}
