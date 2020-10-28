<?php

namespace App\Portal\Http\Middleware;

use App\Exceptions\ForbiddenException;
use App\Helpers\PortalHelper;
use App\Portal\Repositories\CompanyRepository;
use Closure;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class VerifyCompanySlug
 *
 * @package App\Portal\Http\Middleware
 */
class VerifyCompanySlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @param string $active
     *
     * @return mixed
     */
    public function handle($request, Closure $next, string $active = '')
    {
        $companyRepository = app(CompanyRepository::class);

        $companySlug = $request->companySlug();

        if (!$companySlug) {
            throw new NotFoundHttpException(__('exception.not_found_slug'));
        }

        $company = $companyRepository->findBySlug($request->companySlug(), PortalHelper::id());

        if (!$company) {
            throw new NotFoundHttpException(__('exception.not_found_slug'));
        }

        if ($active && !$company->isActive()) {
            if (!auth()->guest()) {
                auth()->logout();
            }
            return response()->error([
                'error' => 'auth.inactive_company',
            ], __('auth.inactive_company'), 403);
        }

        return $next($request);
    }
}
