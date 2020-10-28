<?php

namespace App\Portal\Gates;

use App\Portal\Models\User;
use App\Models\LeasingCondition;

class Portal
{
    public function portalLeasingCondition(User $user, LeasingCondition $leasingCondition)
    {
        return $user->portal_id == $leasingCondition->portal_id;
    }
}
