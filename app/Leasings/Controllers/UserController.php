<?php
namespace App\Leasings\Controllers;

use App\Leasings\Requests\AccessoriesReminderRequest;
use App\Leasings\Services\UserService;
use App\Portal\Models\User;
use Illuminate\Routing\Controller;

class UserController extends Controller
{

    public function sendAccessoriesReminder(User $user, AccessoriesReminderRequest $request)
    {
        UserService::sendAccessoriesReminder($user);
        return response()->json(new \stdClass());
    }
}
