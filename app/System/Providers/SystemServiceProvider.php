<?php

namespace App\System\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class SystemServiceProvider
 *
 * @package App\System\Providers
 */
class SystemServiceProvider extends ServiceProvider
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
