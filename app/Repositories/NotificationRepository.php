<?php

namespace App\Repositories;

use App\Helpers\PortalHelper;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use App\Models\Notification;
use App\Repositories\BaseRepository;
use App\System\Repositories\UserRepository as SystemUserRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Portal\Models\Role;

/**
 * Class NotificationRepository
 *
 * @package App\Repositories
 *
 */
class NotificationRepository extends BaseRepository
{
    /**
     * NotificationRepository constructor.
     *
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->model = $notification;
    }

    /**
     * @inheritdoc
     */
    public function list(array $params, array $relationships = []): LengthAwarePaginator
    {
        $unread = (!empty($params['unread'])) ? $params['unread'] : false;

        $type = PortalHelper::getPortal() ? User::class : SystemUser::class;
        $query = $this->newQuery();

        $query->select([
            'notifications.id',
            'notifiable_id',
            'notifications.read_at',
            'data',
            'created_at'
        ])
        ->where(
            [
                ['notifications.notifiable_id', '=', AuthHelper::id()],
                ['notifiable_type', $type],
            ]
        )->orderBy('created_at', 'desc');

        if ($unread) {
            $params['per_page'] = 10;
            $query->whereNull('notifications.read_at');
        }
        
        return $this->processList($query, $params, $relationships);
    }

    /**
     *
     * @return Collection
     * @throws \Exception
     */
    public function senders($users): Collection
    {
        if (!PortalHelper::getPortal()) {
            return User::where('status_id', '=', User::STATUS_ACTIVE)->get();
        }

        switch (AuthHelper::role()) {
            case Role::ROLE_EMPLOYEE:
                $result = User::where([
                                ['company_id', '=', AuthHelper::companyId()],
                                ['status_id', '=', User::STATUS_ACTIVE],
                                ['id', '!=', AuthHelper::id()]
                              ])->whereHas('roles', function ($query) {
                                  $query->where('name', Role::ROLE_COMPANY_ADMIN);
                              })->get();
                break;
            case Role::ROLE_PORTAL_ADMIN:
                $portalUsers = User::where([
                                    ['portal_id', '=', PortalHelper::id()],
                                    ['status_id', '=', User::STATUS_ACTIVE],
                                    ['id', '!=', AuthHelper::id()]
                                ])->whereHas('roles', function ($query) {
                                    $query->where('name', Role::ROLE_PORTAL_ADMIN);
                                })->get();
                $systemUsers= app(SystemUserRepository::class)->all();
                $otherUsers = User::where([
                                    ['portal_id', '=', PortalHelper::id()],
                                    ['status_id', '=', User::STATUS_ACTIVE]
                                ])->whereHas('roles', function ($query) {
                                    $query->where('name', Role::ROLE_COMPANY_ADMIN)
                                        ->orWhere('name', Role::ROLE_EMPLOYEE);
                                })->get();
                $result = collect();
                foreach ($portalUsers as $user) {
                    $result->push($user);
                }
                foreach ($systemUsers as $user) {
                    $result->push($user);
                }
                foreach ($otherUsers as $user) {
                    $result->push($user);
                }
                break;
            case Role::ROLE_COMPANY_ADMIN:
                $portalUsers = User::where([
                                    ['portal_id', '=', PortalHelper::id()],
                                    ['status_id', '=', User::STATUS_ACTIVE]
                                ])->whereHas('roles', function ($query) {
                                    $query->where('name', Role::ROLE_PORTAL_ADMIN);
                                })->get();
                $otherUsers = User::where([
                                ['company_id', '=', AuthHelper::companyId()],
                                ['status_id', '=', User::STATUS_ACTIVE],
                                ['id', '!=', AuthHelper::id()]
                              ])->whereHas('roles', function ($query) {
                                  $query->where('name', Role::ROLE_COMPANY_ADMIN)
                                        ->orWhere('name', Role::ROLE_EMPLOYEE);
                              })->get();
                $result = collect();
                foreach ($portalUsers as $user) {
                    $result->push($user);
                }
                foreach ($otherUsers as $user) {
                    $result->push($user);
                }
                break;
        }
        return $result;
    }
}
