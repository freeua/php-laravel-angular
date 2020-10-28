<?php
namespace App\Documents\Exceptions;

use App\Exceptions\ControlledException;

class FileTooBigException extends ControlledException
{
    public function __construct()
    {
        $message = 'User has reached the maximum amount of contracts';
        parent::__construct($message, 422, 'fileTooBig');
    }
}
