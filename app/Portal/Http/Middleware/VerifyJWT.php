<?php

namespace App\Portal\Http\Middleware;

use App\Exceptions\TokenExpiredError;
use App\Portal\Events\Portal\Verified;
use App\Portal\Models\User;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Collection;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class VerifyJWT
{
    public function handle($request, Closure $next)
    {
        /** @var Collection $payload */
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
