<?php

namespace App\Portal\Http\Controllers\V1;

use App\Portal\Helpers\AuthHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\V1\UpdatePolicyRequest;
use App\Portal\Http\Requests\V1\UpdateProfileRequest;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\User;
use App\Portal\Services\UserService;

/**
 * Class ProfileController
 *
 * @package App\Portal\Http\Controllers|V1
 */
class ProfileController extends Controller
{
    /** @var UserService */
    private $userService;

    /**
     * ProfileController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        parent::__construct();

        $this->userService = $userService;
    }

    /**
     * View user's profile
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function view()
    {
        /** @var User $user */
        $user = AuthHelper::user();

        return response()->success(new UserResource($user->load('company')));
    }

    /**
     * Update user data
     *
     * @param UpdateProfileRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $this->userService->updateProfile($request->validated());

        return $user
            ? response()->success(new UserResource($user->load('company')))
            : response()->error([__('profile.update.failed')], __('profile.update.failed'));
    }

    /**
     * @param UpdatePolicyRequest $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function updatePolicy(UpdatePolicyRequest $request)
    {
        $user = $this->userService->updateProfile($request->validated());

        return $user
            ? response()->success(new UserResource($user->load('company')))
            : response()->error([__('profile.update.failed')], __('profile.update.failed'));
    }
}
