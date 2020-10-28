<?php

namespace App\Providers;

use App\Models\Companies\Company;
use App\Models\Portal;
use App\Modules\TechnicalServices\Models\TechnicalService;
use App\Observers\CompanyObserver;
use App\Observers\OfferObserver;
use App\Observers\OrderObserver;
use App\Observers\PortalObserver;
use App\Observers\SupplierObserver;
use App\Observers\TechnicalServiceObserver;
use App\Observers\UserObserver;
use App\Portal\Models\Offer;
use App\Portal\Models\Order;
use App\Portal\Models\Product;
use App\Portal\Models\Supplier;
use App\Portal\Models\User;
use App\System\Models\User as SystemUser;
use App\Validator\CustomValidator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Relation::morphMap([
            Product::ENTITY => Product::class,
            Offer::ENTITY => Offer::class,
            Order::ENTITY => Order::class,
            Company::ENTITY => Company::class,
            Portal::ENTITY => Portal::class,
            Supplier::ENTITY => Supplier::class,
            User::class => User::class,
            SystemUser::class => SystemUser::class,
        ]);

        Carbon::serializeUsing(function (Carbon $carbon) {
            return $carbon->format(\DateTime::ISO8601);
        });

        \Validator::resolver(function ($translator, $data, $rules, $messages) {
            return new CustomValidator($translator, $data, $rules, $messages);
        });

        if (config('app.debug')) {
            \DB::enableQueryLog();
        }

        Offer::observe(OfferObserver::class);
        Order::observe(OrderObserver::class);
        Supplier::observe(SupplierObserver::class);
        Portal::observe(PortalObserver::class);
        Company::observe(CompanyObserver::class);
        User::observe(UserObserver::class);
        TechnicalService::observe(TechnicalServiceObserver::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
