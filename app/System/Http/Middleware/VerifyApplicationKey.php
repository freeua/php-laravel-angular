<?php

namespace App\System\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;

/**
 * Class VerifyApplicationKey
 *
 * @package App\System\Http\Middleware
 */
class VerifyApplicationKey
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
        if (request()->appKey() != config('app.system_application_key')) {
            throw new AuthenticationException(__('exception.unauthenticated_app_key'));
        }

        return $next($request);
    }
}
