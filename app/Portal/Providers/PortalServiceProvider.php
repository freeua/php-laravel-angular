<?php

namespace App\Portal\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class PortalServiceProvider
 *
 * @package App\Portal\Providers
 */
class PortalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        // Register last.
        $this->app->register(EventServiceProvider::class);
    }
}
