<?php

namespace App\Http\Middleware;

use App\Exceptions\ForbiddenException;
use Closure;

/**
 * Class VerifyPasswordAge
 *
 * @package App\Portal\Http\Middleware
 */
class VerifyPasswordAge
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     * @throws ForbiddenException
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->isPasswordExpired()) {
            throw new ForbiddenException(__('exception.password_age'));
        }

        return $next($request);
    }
}
