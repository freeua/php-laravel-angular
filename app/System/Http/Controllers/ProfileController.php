<?php

namespace App\System\Http\Controllers;

use App\System\Http\Requests\UpdateProfileRequest;
use App\System\Http\Resources\UserResource;
use App\System\Models\User;
use App\System\Services\UserService;
use Illuminate\Support\Facades\Auth;

/**
 * Class ProfileController
 *
 * @package App\System\Http\Controllers
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
        $user = Auth::user();

        return response()->success(new UserResource($user));
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
            ? response()->success(new UserResource($user))
            : response()->error([__('profile.update.failed')], __('profile.update.failed'));
    }
}
