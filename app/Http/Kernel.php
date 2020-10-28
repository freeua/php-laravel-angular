<?php

namespace App\Http;

use App\Http\Middleware\MailgunWebhook;
use App\Http\Middleware\OAuthOrJWT;
use App\Http\Middleware\PartnerJWT;
use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * Class Kernel
 *
 * @package App\Http
 */
class Kernel extends HttpKernel
{
    protected $middlewarePriority = [
        Middleware\CorsHeaders::class,
        \App\Portal\Http\Middleware\VerifyCompanySlug::class,
        \App\System\Http\Middleware\VerifyApplicationKey::class,
        \App\Portal\Http\Middleware\VerifyJWT::class,
        \App\System\Http\Middleware\VerifyJWT::class,
        \Illuminate\Auth\Middleware\Authenticate::class,
        Middleware\BasicAuthMlf::class,
        \App\Portal\Http\Middleware\VerifyUserCompanySlug::class,
    ];

    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        Middleware\CorsHeaders::class,
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        Middleware\TrustProxies::class,
        Middleware\Version::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:300,1',
            'bindings',
        ]
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'jwt.valid' => Middleware\ValidJWT::class,
        'portal.verify_domain' => \App\Portal\Http\Middleware\VerifyDomain::class,
        'portal.company_slug' => \App\Portal\Http\Middleware\VerifyCompanySlug::class,
        'system.app_key' => \App\System\Http\Middleware\VerifyApplicationKey::class,
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'basicAuthMlf' => Middleware\BasicAuthMlf::class,
        'pwd_age' => Middleware\VerifyPasswordAge::class,
        'portal.jwt' => \App\Portal\Http\Middleware\VerifyJWT::class,
        'portal.user_company_slug' => \App\Portal\Http\Middleware\VerifyUserCompanySlug::class,
        'system.jwt' => \App\System\Http\Middleware\VerifyJWT::class,
        'portal.user' => \App\Portal\Http\Middleware\VerifyUser::class,
        'guest' =>  Middleware\Guest::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'oauth_or_jwt' => Middleware\OAuthOrJWT::class,
        'partner_jwt' => Middleware\PartnerJWT::class,
        'mailgun-webhook' => MailgunWebhook::class,
        'oauth' => Middleware\OAuthOrJWT::class,
        'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
    ];
}
