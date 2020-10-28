<?php
namespace App\Exceptions;

class MaximumPriceError extends ControlledException
{
    public function __construct()
    {
        $message = 'Offer price (agreed purchase price + accessories discounted price) should be less than máximum ' . '
        configured on company or user';
        parent::__construct($message, 422, 'maximumPriceError');
    }
}
