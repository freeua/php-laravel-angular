<?php
namespace App\Leasings\Requests;

use App\Http\Requests\ApiRequest;
use App\Partners\Models\Partner;
use App\Portal\Models\User;

class AccessoriesReminderRequest extends ApiRequest
{
    public function authorize()
    {
        $requester = request()->requester;
        $user = request()->route('user');
        if ($requester instanceof Partner && $user instanceof User) {
            return $requester->portals->keyBy('id')->has($user->portal_id);
        }
        return false;
    }

    public function rules()
    {
        return [];
    }
}
