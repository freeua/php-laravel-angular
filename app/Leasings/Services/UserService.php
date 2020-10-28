<?php

namespace App\Leasings\Services;

use App\Portal\Models\User;
use App\Portal\Notifications\Order\ReminderAccessories;

class UserService
{
    public static function sendAccessoriesReminder(User $user)
    {
        $user->notify(new ReminderAccessories($user));
        return response()->json(new \stdClass());
    }
}
