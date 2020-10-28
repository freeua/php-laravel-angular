<?php

namespace App\Http\Requests\Notifications;

use App\Models\Notification;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;

class ViewNotificationRequest extends \App\Http\Requests\ApiRequest
{
    public function authorize(): bool
    {
        $notification = $this->route('notification');
        if ($notification instanceof Notification) {
            return $this->checkUserAccess($notification);
        }
        return false;
    }

    public function rules()
    {
        return [];
    }

    private function checkUserAccess(Notification $notification): bool
    {
        $user = auth()->user();
        if ($user instanceof User) {
            $notifiableUser = $notification->notifiable;
            if (!$notifiableUser || !$notifiableUser instanceof User || $notifiableUser->id !== $user->id) {
                return false;
            }
            return true;
        } elseif ($user instanceof SystemUser) {
            $notifiableUser = $notification->notifiable;
            if (!$notifiableUser || !$notifiableUser instanceof SystemUser || $notifiableUser->id !== $user->id) {
                return false;
            }
            return true;
        }
        return false;
    }
}
