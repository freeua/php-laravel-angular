<?php

namespace App\System\Services;

use App\Models\Permission;
use App\Portal\Models\User as PortalUser;
use App\System\Repositories\PortalRepository;
use App\System\Repositories\PortalUserRepository;
use App\Portal\Services\UserService;

/**
 * Class PortalUserService
 *
 * @package App\System\Services
 */
class PortalUserService
{
    /** @var PortalUserRepository */
    private $portalUserRepository;
    /** @var PortalRepository */
    private $portalRepository;
    /** @var UserService */
    private $userService;

    /**
     * PortalUserService constructor.
     *
     * @param PortalUserRepository    $portalUserRepository
     * @param PortalRepository        $portalRepository
     * @param UserService             $userService
     */
    public function __construct(
        PortalUserRepository $portalUserRepository,
        PortalRepository $portalRepository,
        UserService $userService
    ) {
        $this->portalUserRepository = $portalUserRepository;
        $this->portalRepository = $portalRepository;
        $this->userService = $userService;
    }

    /**
     * @param array $data
     *
     * @return PortalUser|false
     * @throws \Exception
     */
    public function create(array $data)
    {
        $portal = $this->portalRepository->find($data['portal_id']);
        \DB::beginTransaction();
        $portalUser = $this->userService->createAdmin($data, $portal);
        if ($data['hasEditPermission']) {
            $portalUser->guard_name = 'portal';
            $portalUser->givePermissionTo([Permission::EDIT_PORTAL_DATA]);
        }
        \DB::commit();
        return $portalUser;
    }

    /**
     * @param PortalUser $user
     * @param array $data
     *
     * @return PortalUser|false
     * @throws \Exception
     */
    public function update(PortalUser $user, array $data)
    {
        \DB::beginTransaction();
        $updated = $this->portalUserRepository->update($user->id, $data);
        $user->guard_name = 'portal';
        if ($data['hasEditPermission']) {
            $user->givePermissionTo([Permission::EDIT_PORTAL_DATA]);
        } else {
            $user->revokePermissionTo(Permission::EDIT_PORTAL_DATA);
        }
        \DB::commit();
        return $updated;
    }

    /**
     * @param PortalUser $portalUser
     *
     * @return bool|null
     * @throws \Exception
     */
    public function delete(PortalUser $portalUser)
    {
        return $this->portalUserRepository->delete($portalUser->id);
    }

    /**
     * @param PortalUser $user
     *
     * @return array|bool
     */
    public function loginAs(PortalUser $user)
    {
        if (!$user->isActive()) {
            return false;
        }

        $portal = $user->portal;

        $login = auth() ->guard(config('auth.portal_guard'))
                        ->claims([PortalUser::JWT_APP_KEY => $portal->domain])
                        ->byId($user->id);
        if (!$login) {
            return false;
        }

        return [
            'token'       => [
                'access_token' => auth()->guard(config('auth.portal_guard'))->tokenById($user->id),
                'expires_in'   => auth()->guard(config('auth.portal_guard'))->factory()->getTTL() * 60
            ],
            'user'        => $user,
            'redirect_to' => $user->getFrontendFullUrl()
        ];
    }
}
