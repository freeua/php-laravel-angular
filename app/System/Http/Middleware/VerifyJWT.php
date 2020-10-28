<?php

namespace App\System\Http\Middleware;

use App\Exceptions\TokenExpiredError;
use App\System\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use App\System\Events\System\Verified;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

/**
 * Class VerifyJWT
 *
 * @package App\System\Http\Middleware
 */
class VerifyJWT
{
    public function handle($request, Closure $next)
    {
        try {
            $payload = auth()->payload();
        } catch (TokenExpiredException $error) {
            throw new TokenExpiredError();
        }

        if (request()->appKey() != $payload->get(User::JWT_APP_KEY)) {
            auth()->logout();
            throw new AuthenticationException(__('exception.unauthenticated_jwt'));
        }

        event(new Verified);

        return $next($request);
    }
}
