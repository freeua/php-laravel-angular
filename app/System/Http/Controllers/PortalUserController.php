<?php

namespace App\System\Http\Controllers;

use App\Portal\Http\Resources\V1\UserResource;
use App\System\Http\Requests\CreatePortalUserRequest;
use App\System\Http\Requests\UpdatePortalUserRequest;
use App\System\Http\Resources\PortalUserResource;
use App\Portal\Models\User as PortalUser;
use App\System\Services\PortalUserService;

/**
 * Class PortalUserController
 *
 * @package App\System\Http\Controllers
 */
class PortalUserController extends Controller
{
    /** @var PortalUserService */
    private $portalUserService;

    /**
     * PortalUserController constructor.
     *
     * @param PortalUserService $portalUserService
     */
    public function __construct(PortalUserService $portalUserService)
    {
        parent::__construct();

        $this->portalUserService = $portalUserService;
    }

    /**
     * Create a new portal user
     *
     * @param CreatePortalUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function create(CreatePortalUserRequest $request)
    {
        $user = $this->portalUserService->create($request->validated());

        return $user
            ? response()->success(new PortalUserResource($user))
            : response()->error([__('user.create.failed')], __('user.create.failed'));
    }

    /**
     * Update user data
     *
     * @param PortalUser $user
     * @param UpdatePortalUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(PortalUser $user, UpdatePortalUserRequest $request)
    {
        $user = $this->portalUserService->update($user, $request->validated());

        return $user
            ? response()->success(new PortalUserResource($user))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
    }

    /**
     * Delete user
     *
     * @param PortalUser $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(PortalUser $user)
    {
        $result = $this->portalUserService->delete($user);

        return $result
            ? response()->success()
            : response()->error([__('user.delete.failed')], __('user.delete.failed'));
    }

    /**
     * View user
     *
     * @param PortalUser $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view(PortalUser $user)
    {
        return response()->success(new PortalUserResource($user));
    }

    /**
     * Login as portal user
     *
     * @param PortalUser $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function loginAs(PortalUser $user)
    {
        $result = $this->portalUserService->loginAs($user);

        return $result
            ? response()->success([
                'token'       => $result['token'],
                'user'        => new UserResource($result['user']),
                'redirect_to' => $result['redirect_to']
            ])
            : response()->error([__('user.login_as.failed')], __('user.login_as.failed'));
    }
}
