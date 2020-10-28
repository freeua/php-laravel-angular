<?php

namespace App\Portal\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Config;

/**
 * Class EnforceTenancy
 *
 * @package App\Portal\Http\Middleware
 */
class EnforceTenancy
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Config::set('database.default', 'tenant');

        return $next($request);
    }
}
