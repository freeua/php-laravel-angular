<?php

namespace App\System\Http\Controllers\Auth;

use App\System\Http\Controllers\Controller;
use App\System\Http\Requests\LoginRequest;
use App\System\Http\Resources\UserResource;
use App\System\Models\User;
use Illuminate\Http\JsonResponse;

/**
 * Class LoginController
 *
 * @package App\System\Http\Controllers\Auth
 */
class LoginController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
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

        $token = auth()->attempt($loginData);

        if (!$token) {
            return response()->error(
                [
                    'email' => [__('auth.failed')]
                ],
                __('auth.failed'),
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return response()->success([
            'token' => [
                'access_token' => $token,
                'expires_in'   => auth()->factory()->getTTL() * 60
            ],
            'user' => new UserResource(auth()->user())
        ]);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
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
