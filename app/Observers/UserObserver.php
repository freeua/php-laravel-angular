<?php

namespace App\Observers;

use App\Portal\Models\User;

class UserObserver
{
    public function created(User $user)
    {
        $user->code = $user->generateCode($user->id, 6, '', 'MA-');
        $user->save();
    }
}
