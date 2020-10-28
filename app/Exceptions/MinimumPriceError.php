<?php
namespace App\Exceptions;

class MinimumPriceError extends ControlledException
{
    public function __construct()
    {
        $message = 'Offer price (agreed purchase price + accessories discounted price) should be greater than minimum ' . '
        configured on company or user';
        parent::__construct($message, 422, 'minimumPriceError');
    }
}
