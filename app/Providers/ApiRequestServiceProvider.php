<?php

namespace App\Providers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\ServiceProvider;

/**
 * Class ApiRequestServiceProvider
 *
 * @package App\Providers
 */
class ApiRequestServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * Return application key from request header.
         *
         * @return string
         * @instantiated
         */
        Request::macro('appKey', function () {
            /** @var \Illuminate\Support\Facades\Request $this */
            return $this->header('application-key');
        });

        /**
         * Return application key from request header.
         *
         * @return string
         * @instantiated
         */
        Request::macro('module', function () {
            /** @var \Illuminate\Support\Facades\Request $this */
            return $this->header('X-Benefit-Portal-Module');
        });

        /**
         * Return company slug from request header.
         *
         * @return string
         * @instantiated
         */
        Request::macro('companySlug', function () {
            /** @var \Illuminate\Support\Facades\Request $this */
            return $this->header('company-slug');
        });
    }
}
