<?php

namespace App\Services;

use App\Http\Requests\Notifications\CreateNotificationRequest;
use App\Portal\Models\User;
use App\Portal\Models\Supplier;
use App\Models\Notification;
use App\Models\Companies\Company;
use App\System\Models\User as SystemUser;
use App\Portal\Notifications\CustomNotification;

class NotificationService
{
    public function create(CreateNotificationRequest $request)
    {
        try {
            $data = $this->requestToArray($request);
            $this->notify($data);
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    private function notify(Array $data)
    {
        switch ($data['type']) {
            case Notification::PORTAL:
                $type = User::query()->find($data['to']);
                break;
            case Notification::SYSTEM:
                $type = SystemUser::query()->find($data['to']);
                break;
            case Notification::COMPANY:
                $type = Company::query()->find($data['to']);
                break;
            case Notification::SUPPLIER:
                $type = Supplier::query()->find($data['to']);
                break;
        }
        $type->notify(new CustomNotification($data));
    }

    private function requestToArray(CreateNotificationRequest $request): array
    {
        return [
            'to' => $request->input('to.id'),
            'type' => $request->input('to.type'),
            'data' => [
                'subject' => $request->input('subject'),
                'body'    => $request->input('body'),
            ]
        ];
    }
}
