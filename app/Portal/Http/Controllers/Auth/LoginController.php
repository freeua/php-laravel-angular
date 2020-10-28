<?php

namespace App\Portal\Http\Controllers\Auth;

use App\Helpers\PortalHelper;
use App\Portal\Http\Controllers\Controller;
use App\Portal\Http\Requests\LoginRequest;
use App\Portal\Http\Resources\V1\UserResource;
use App\Portal\Models\User;
use App\Portal\Repositories\UserRepository;
use App\Portal\Services\UserService;
use Auth;
use DB;
use Illuminate\Http\JsonResponse;

/**
 * Class LoginController
 *
 * @package App\Portal\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /** @var UserRepository */
    private $userRepository;
    /** @var UserService */
    private $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserService    $userService
     * @param UserRepository $userRepository
     */
    public function __construct(UserService $userService, UserRepository $userRepository)
    {
        parent::__construct();

        $this->userService = $userService;
        $this->userRepository = $userRepository;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $loginData = $request->validated();
        $loginData['status_id'] = User::STATUS_ACTIVE;
        if (!auth()->validate($loginData)) {
            return response()->error(
                [
                    'email' => [__('auth.failed')]
                ],
                __('auth.failed'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        if (!$this->userService->checkEmailAccess($request->get('email'))) {
            $error = $this->userService->getEmailAccessError($request->get('email'));

            return response()->error(
                [
                    'email' => [$error]
                ],
                $error,
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $user = $this->userRepository->findByEmail($request->get('email'));
        if ($user->isSupplier() && !$user->supplier->portals->keyBy('id')->has(PortalHelper::getPortal()->id)) {
            return response()->error(
                new \stdClass,
                __('auth.wrong_login_credentials'),
                JsonResponse::HTTP_FORBIDDEN
            );
        } elseif (!$user->isSupplier() && $user->portal_id != PortalHelper::id()) {
            return response()->error(
                [
                    'email' => [__('auth.failed')]
                ],
                __('auth.failed'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
        if (!$this->userService->checkUserAccessToModule($request->route()->getPrefix(), $request->companySlug(), $user)) {
            return response()->error(
                [
                    'redirect_to' => $user->getLoginPath()
                ],
                __('auth.wrong_login_credentials'),
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        return response()->success([
            'token' => [
                'access_token' => auth()->attempt($loginData),
                'expires_in'   => auth()->factory()->getTTL() * 60
            ],
            'user'  => new UserResource(auth()->user())
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $user = auth()->user();

        if ($user && !$this->userService->checkEmailAccess($user->email)) {
            $error = $this->userService->getEmailAccessError($user->email);

            auth()->logout();

            return response()->error(
                [
                    'email' => [$error]
                ],
                $error,
                JsonResponse::HTTP_FORBIDDEN
            );
        }

        return response()->success([
            'access_token' => auth()->refresh(),
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->success();
    }
}
