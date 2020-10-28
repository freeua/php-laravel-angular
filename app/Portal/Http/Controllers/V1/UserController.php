<?php

namespace App\Portal\Http\Controllers\V1;

use App\Http\Requests\DefaultListRequest;
use App\Http\Resources\PermissionResource;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\CreateUserRequest;
use App\Portal\Http\Requests\V1\UpdateUserDetailsRequest;
use App\Portal\Http\Requests\V1\UpdateUserPermissionsRequest;
use App\Portal\Http\Requests\V1\UpdateUserRequest;
use App\Portal\Http\Resources\V1\ListCollections\UserListCollection;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\User;
use App\Portal\Repositories\UserRepository;
use App\Portal\Services\UserService;

/**
 * Class UserController
 *
 * @package App\Portal\Http\Controllers\V1
 */
class UserController extends Controller
{
    /** @var UserService */
    private $userService;
    /** @var UserRepository */
    private $userRepository;

    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    public function index(DefaultListRequest $request)
    {
        $users = $this->userRepository->list($request->validated());

        return response()->success(new UserListCollection($users));
    }

    public function create(CreateUserRequest $request)
    {
        $user = $this->userService->selfCreate($request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.create.failed')], __('user.create.failed'));
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $user = $this->userService->selfUpdate($user, $request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
    }

    /**
     * @param User $user
     * @param UpdateUserDetailsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDetails(User $user, UpdateUserDetailsRequest $request)
    {
        $user = $this->userService->selfUpdateDetails($user, $request->validated());

        return $user
            ? response()->success(new UserResource($user))
            : response()->error([__('user.update.failed')], __('user.update.failed'));
    }

    /**
     * Update a permission
     *
     * @param User              $user
     * @param UpdateUserRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePermissions(User $user, UpdateUserPermissionsRequest $request)
    {
        $permissions = $request->validated()['permissions'];
        $this->userService->updatePermissions($user, $permissions);

        return response()->json(new UserResource($user));
    }

    public function listPermissions()
    {
        $permissions = $this->userService->listPermissions('company');
        return response()->success(PermissionResource::collection($permissions));
    }

    public function view(User $user)
    {
        return response()->success(new UserResource($user));
    }

    /**
     * Delete user
     *
     * @param User $user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function delete(User $user)
    {
        return response()->error([__('user.delete.failed')], __('user.delete.failed'));
    }
}
