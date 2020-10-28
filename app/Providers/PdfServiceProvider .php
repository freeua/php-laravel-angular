<?php

namespace App\Providers;

use App\Libraries\Pdf\PdfCreator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class PdfServiceProvider
 *
 * @package App\Providers
 */
class PdfServiceProvider extends BaseServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /*
    * Bootstrap the application service
    *
    * @return void
    */
    public function boot()
    {
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('pdf', function ($app) {
            return new PdfCreator();
        });
    }
}
