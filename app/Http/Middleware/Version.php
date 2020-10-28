<?php
namespace App\Http\Middleware;

use Closure;

class Version
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);
        if (method_exists($response, 'header')) {
            $response->header('App-Version', config('app.version'));
        }
        return $response;
    }
}
