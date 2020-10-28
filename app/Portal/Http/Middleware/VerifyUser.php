<?php

namespace App\Portal\Http\Middleware;

use Closure;
use App\Portal\Models\User;
use Illuminate\Auth\AuthenticationException;

/**
 * Class VerifyUser
 *
 * @package App\Portal\Http\Middleware
 */
class VerifyUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        /** @var User $user */
        $user = auth()->user();

        if (!$user->isActive()) {
            auth()->logout();
            throw new AuthenticationException(__('exception.unauthenticated'));
        }

        return $next($request);
    }
}
