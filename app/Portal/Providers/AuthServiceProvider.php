<?php

namespace App\Portal\Providers;

use Illuminate\Support\Facades\Gate;

/**
 * Class AuthServiceProvider
 *
 * @package App\Providers\Portal
 */
class AuthServiceProvider extends \Illuminate\Foundation\Support\Providers\AuthServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('access-leasing-condition', 'App\Portal\Gates\Portal@portalLeasingCondition');

        Gate::define('companies.manage-users', 'App\Portal\Gates\Company@manageUsers');
        Gate::define('companies.edit-company-data', 'App\Portal\Gates\Company@editCompanydata');
        Gate::define('companies.read-company-data', 'App\Portal\Gates\Company@readCompanyData');
        Gate::define('access-company-offer', 'App\Portal\Gates\Company@offer');
        Gate::define('access-company-order', 'App\Portal\Gates\Company@order');
        Gate::define('access-company-contract', 'App\Portal\Gates\Company@contract');
        Gate::define('access-company-file', 'App\Portal\Gates\Company@file');

        Gate::define('access-user-widget', 'App\Portal\Gates\Company@widget');

        Gate::define('access-supplier-user', 'App\Portal\Gates\Supplier@user');
        Gate::define('access-supplier-offer', 'App\Portal\Gates\Supplier@offer');
        Gate::define('access-supplier-file', 'App\Portal\Gates\Supplier@file');
        Gate::define('access-supplier-order', 'App\Portal\Gates\Supplier@order');
        Gate::define('access-technical-service', 'App\Portal\Gates\Supplier@technicalService');

        Gate::define('access-employee-offer', 'App\Portal\Gates\Employee@offer');
        Gate::define('access-employee-order', 'App\Portal\Gates\Employee@order');
        Gate::define('access-employee-contract', 'App\Portal\Gates\Employee@contract');
        Gate::define('access-employee-file', 'App\Portal\Gates\Employee@file');
    }
}
