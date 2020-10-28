<?php

namespace App\Portal\Http\Middleware;

use App\Exceptions\WrongRouteException;
use App\Portal\Helpers\AuthHelper;
use App\Portal\Models\User;
use Closure;

/**
 * Class VerifyUserCompanySlug
 *
 * @package App\Portal\Http\Middleware
 */
class VerifyUserCompanySlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *     *
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->companySlug() != AuthHelper::companySlug()) {
            /** @var User $user */
            $user = auth()->user();
            throw new WrongRouteException($user->getModulePath());
        }

        return $next($request);
    }
}
