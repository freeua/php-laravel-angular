<?php
namespace App\Exceptions;

class LeasingBudgetReached extends ControlledException
{
    public function __construct()
    {
        $message = __('offer.accept.leasing_budget');
        parent::__construct($message, 422, 'leasingBudgetReached');
    }
}
