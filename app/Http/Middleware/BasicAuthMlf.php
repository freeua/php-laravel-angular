<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Hashing\HashManager;
use Symfony\Component\HttpFoundation\Response;

class BasicAuthMlf
{
    public function handle($request, Closure $next)
    {
        $user = isset(request()->server()['PHP_AUTH_USER']) ? request()->server()['PHP_AUTH_USER'] : null;
        $password = isset(request()->server()['PHP_AUTH_USER']) ? request()->server()['PHP_AUTH_PW'] : null;
        $authUser = env('MERCATOR_EXPORT_USER');
        $bcryptedAuthPassword = env('BCRYPTED_MERCATOR_EXPORT_PASSWORD');
        /** @var HashManager $hashManager */
        $hashManager = app(HashManager::class);
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        if ($authUser === $user && $hashManager->check($password, $bcryptedAuthPassword)) {
            return $next($request);
        }
        header('HTTP/1.1 401 Authorization Required');
        header('WWW-Authenticate: Basic realm="Access denied"');
        return new Response('Access denied', 401);
    }
}
