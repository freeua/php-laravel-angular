<?php

namespace App\Portal\Http\Middleware;

use App\Helpers\PortalHelper;
use Closure;
use DB;
use Illuminate\Auth\AuthenticationException;
use App\System\Repositories\PortalRepository;
use Illuminate\Log\Logger;

/**
 * Class VerifyDomain
 *
 * @package App\Portal\Http\Middleware
 */
class VerifyDomain
{
    /**
     * Handle an incoming request to extract the domain of the request. If the request has the origin
     * header, we'll take that. If not, we'll search for Application-Key header.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     *     *
     * @return mixed
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next)
    {
        $domain = parse_url($request->headers->get('origin'), PHP_URL_HOST);
        $portalRepository = app(PortalRepository::class);
        $portal = null;
        if ($domain) {
            $portal = $portalRepository->findByDomain($domain);
        }
        if (!$portal && !request()->appKey()) {
            throw new AuthenticationException(__('exception.unauthenticated_portal'));
        }
        if (!$portal && request()->appKey()) {
            $portal = !$portalRepository->findByAppKey(request()->appKey());
            if (!$portal) {
                throw new AuthenticationException(__('exception.unauthenticated_portal'));
            }
        }
        PortalHelper::setPortal($portal);
        $response = $next($request);
        return $response;
    }
}
