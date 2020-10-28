<?php

namespace App\Http\Controllers\Notifications;

use App\Models\Notification;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use App\Portal\Http\Controllers\Controller;

use App\Http\Resources\Notification\ListCollection\NotificationListCollection;
use App\Http\Resources\Notification\NotificationResource;
use App\Http\Resources\Notification\SenderResource;
use App\Repositories\NotificationRepository;
use App\Services\NotificationService;
use App\Portal\Repositories\UserRepository;
use App\Http\Requests\NotificationListRequest;
use App\Http\Requests\Notifications\CreateNotificationRequest;
use App\Http\Requests\Notifications\ViewNotificationRequest;
use App\Portal\Helpers\AuthHelper;
use App\Helpers\PortalHelper;
use Carbon\Carbon;

/**
 * Class NotificationController
 *
 * @package App\Portal\Http\Controllers\V1\Base
 */
class NotificationController extends Controller
{
    private $notificationRepository;

    public function __construct(
        NotificationService $notificationService,
        NotificationRepository $notificationRepository
    ) {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->notificationRepository = $notificationRepository;
    }

    public function index(NotificationListRequest $request)
    {
        $notifications = $this->notificationRepository->list($request->validated());
        return response()->pagination(NotificationListCollection::collection($notifications));
    }

    public function view(ViewNotificationRequest $request, Notification $notification)
    {
        if (!$notification->read_at) {
            $this->markAsRead($notification);
            $notification->fresh();
        }
        return response()->json(new NotificationResource($notification));
    }

    public function create(CreateNotificationRequest $request)
    {
        $this->notificationService->create($request);
        return response()->json(new \stdClass());
    }

    public function senders()
    {
        $users = app(UserRepository::class)->all();
        return response()->json(
            SenderResource::collection($this->notificationRepository->senders($users))
        );
    }

    private function markAsRead(Notification $notification)
    {
        $notification->read_at = Carbon::now();
        $notification->save();
    }
}
