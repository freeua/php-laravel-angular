<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 13.06.2019
 * Time: 16:48
 */

namespace App\Exceptions;

class UserIsNotAllowed extends ControlledException
{
    public function __construct()
    {
        $message = 'User is not allowed';
        parent::__construct($message, 422, 'userIsNotAllowed');
    }
}
