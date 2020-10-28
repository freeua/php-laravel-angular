<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

/**
 * Class RouteServiceProvider
 *
 * @package App\Providers
 */
class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';
    protected $portalNamespace = 'App\Portal\Http\Controllers';
    protected $systemNamespace = 'App\System\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapSystemApiRoutes();
        $this->mapPortalApiRoutes();
        $this->mapCompanyApiRoutes();
        $this->mapSupplierApiRoutes();
        $this->mapEmployeeApiRoutes();
        $this->mapPortalsRoutes();
        $this->mapPartnersRoutes();
        $this->mapDocumentsRoutes();
        $this->mapLeasingsRoutes();
        $this->mapTechnicalServicesRoutes();
        $this->mapExternalRoutes();
        $this->mapWebRoutes();
        $this->mapWebhooksRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }

    /**
     * Define the portal api routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapPortalApiRoutes()
    {
        Route::prefix(config('app.portal_api_prefix'))
             ->middleware('api')
             ->namespace($this->portalNamespace)
             ->group(base_path('routes/portal-api.php'));
    }

    protected function mapPortalsRoutes()
    {
        Route::middleware('api')
            ->namespace('App\Portals\Controllers')
            ->group(base_path('routes/portals.php'));
    }

    protected function mapDocumentsRoutes()
    {
        Route::middleware('api')
            ->namespace('App\Documents\Controllers')
            ->group(base_path('routes/documents.php'));
    }

    protected function mapTechnicalServicesRoutes()
    {
        Route::middleware('api')
            ->namespace('App\Modules\TechnicalServices\Controllers')
            ->group(base_path('routes/technical-services.php'));
    }

    protected function mapWebhooksRoutes()
    {
        Route::middleware('api')
            ->namespace('App\Webhooks\Controllers')
            ->group(base_path('routes/webhooks.php'));
    }

    protected function mapPartnersRoutes()
    {
        Route::prefix('partners')
            ->middleware('api')
            ->namespace('App\Partners\Controllers')
            ->group(base_path('routes/partners.php'));
    }

    protected function mapLeasingsRoutes()
    {
        Route::prefix('leasings')
            ->middleware('api')
            ->namespace('App\Leasings\Controllers')
            ->group(base_path('routes/leasings.php'));
    }

    protected function mapExternalRoutes()
    {
        Route::prefix('external')
            ->middleware('api')
            ->namespace('App\ExternalLogin\Controllers')
            ->group(base_path('routes/external-login.php'));
    }
    
    /**
     * Define the company api routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapCompanyApiRoutes()
    {
        Route::prefix(config('app.company_api_prefix'))
             ->middleware('api')
             ->namespace($this->portalNamespace)
             ->group(base_path('routes/company-api.php'));
    }

    /**
     * Define the employee api routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapEmployeeApiRoutes()
    {
        Route::prefix(config('app.employee_api_prefix'))
             ->middleware('api')
             ->namespace($this->portalNamespace)
             ->group(base_path('routes/employee-api.php'));
    }

    /**
     * Define the supplier api routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSupplierApiRoutes()
    {
        Route::prefix(config('app.supplier_api_prefix'))
             ->middleware('api')
             ->namespace($this->portalNamespace)
             ->group(base_path('routes/supplier-api.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSystemApiRoutes()
    {
        Route::prefix(config('app.system_api_prefix'))
             ->middleware('api')
             ->namespace($this->systemNamespace)
             ->group(base_path('routes/system-api.php'));
    }
}
