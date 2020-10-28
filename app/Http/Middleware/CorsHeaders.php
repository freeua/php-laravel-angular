<?php

namespace App\Http\Middleware;

use Asm89\Stack\CorsService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Log\Logger;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\Http\Events\RequestHandled;

/**
 * Class CorsHeaders
 * Based on barryvdh/laravel-cors but we need the hability to dynamically know the System URL and check it if
 * based on a config on .env
 *
 * @package App\Http\Middleware
 */
class CorsHeaders
{
    /** @var Dispatcher $events */
    protected $events;

    /**
     * CorsSystemHeaders constructor.
     * @param CorsService $cors
     * @param Dispatcher $events
     */
    public function __construct(Dispatcher $events)
    {
        $this->events = $events;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $cors = new CorsService([
            'supportsCredentials' => false,
            'allowedOrigins' => ['*'],
            'allowedOriginsPatterns' => [],
            'allowedHeaders' => ['*'],
            'allowedMethods' => ['*'],
            'exposedHeaders' => ['Content-disposition'],
            'maxAge' => 590,
        ]);
        if (! $cors->isCorsRequest($request)) {
            return $next($request);
        }

        if ($cors->isPreflightRequest($request)) {
            return $cors->handlePreflightRequest($request);
        }

        if (! $cors->isActualRequestAllowed($request)) {
            return new Response('Not allowed in CORS policy.', 403);
        }

        // Add the headers on the Request Handled event as fallback in case of exceptions
        if (class_exists(RequestHandled::class)) {
            $this->events->listen(RequestHandled::class, function (RequestHandled $event) use ($cors) {
                $this->addHeaders($cors, $event->request, $event->response);
            });
        } else {
            $this->events->listen('kernel.handled', function (Request $request, Response $response) use ($cors) {
                $this->addHeaders($cors, $request, $response);
            });
        }

        $response = $next($request);
        $this->addHeaders($cors, $request, $response);
        return $response;
    }

    /**
     * @param CorsService $cors
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    protected function addHeaders(CorsService $cors, Request $request, Response $response)
    {
        // Prevent double checking
        if (! $response->headers->has('Access-Control-Allow-Origin')) {
            $response = $cors->addActualRequestHeaders($response, $request);
        }
        return $response;
    }
}
