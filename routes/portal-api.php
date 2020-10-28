<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use App\Portal\Models\Role;

Route::group(['middleware' => ['portal.verify_domain']], function () {
    Route::group(['prefix' => 'v1'], function () {
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'V1\SettingController@index');
            Route::post('/download-leasingable-pdf', 'V1\SettingController@downloadLeasingablePdf');
        });
        Route::group(['middleware' => 'guest:api'], function () {
            Route::post('login', 'Auth\LoginController@login');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/user-info', 'Auth\ResetPasswordController@userInfo');
            Route::post('refresh', 'Auth\LoginController@refresh');
        });

        Route::group([
            'prefix' => 'profile',
            'middleware' => ['auth:api', 'portal.jwt', 'portal.user']
        ], function () {
            Route::get('/', 'V1\ProfileController@view');
            Route::post('/', 'V1\ProfileController@update');
        });

        Route::group(
            ['middleware' => ['auth:api', 'portal.jwt', 'portal.user', 'role:' . Role::ROLE_PORTAL_ADMIN]],
            function () {

                Route::group(['prefix' => 'permissions'], function () {
                    Route::get('/', 'V1\CompanyController@listPermissions');
                });


                Route::post('logout', 'Auth\LoginController@logout');
                Route::group(['middleware' => 'pwd_age'], function () {

                    Route::group(['prefix' => 'users'], function () {
                        Route::get('/', 'V1\UserController@index');
                        Route::post('/', 'V1\UserController@create');
                        Route::get('/company-permissions', 'V1\UserController@listPermissions');
                        Route::get('/{user}', 'V1\UserController@view');
                        Route::post('/{user}', 'V1\UserController@update');
                        Route::put('/{user}', 'V1\UserController@updateDetails');
                        Route::delete('/{user}', 'V1\UserController@delete');
                        Route::put('/{user}/permissions', 'V1\UserController@updatePermissions');
                    });

                    Route::group(['prefix' => 'companies'], function () {
                        Route::get('/', 'V1\CompanyController@index');
                        Route::post('/', 'V1\CompanyController@create');
                        Route::get('/all', 'V1\CompanyController@all');
                        Route::get('/homepage', 'V1\CompanyController@getDefaultHomepage');
                        Route::put('/homepage', 'V1\CompanyController@updateDefaultHomepage');
                        Route::get('/{company}', 'V1\CompanyController@view');
                        Route::put('/{company}', 'V1\CompanyController@update');
                        Route::get('/{company}/homepage', 'V1\CompanyController@getHomepage');
                        Route::put('/{company}/homepage', 'V1\CompanyController@updateHomepage');
                        Route::get('/{company}/employees', 'V1\CompanyController@listEmployees');
                        Route::get('/{company}/admins', 'V1\CompanyController@listAdmins');
                        Route::post('/{company}/leasing-conditions',
                            '\App\Http\Controllers\Companies\CompanyLeasingConditionsController@addLeasingcondition');
                        Route::put(
                            '/{company}/leasing-conditions/{leasingCondition}/activate',
                            '\App\Http\Controllers\Companies\CompanyLeasingConditionsController@activateLeasingcondition');
                        Route::put(
                            '/{company}/leasing-conditions/{leasingCondition}/deactivate',
                            '\App\Http\Controllers\Companies\CompanyLeasingConditionsController@deactivateLeasingcondition');
                        Route::delete(
                            '/{company}/leasing-conditions/{leasingCondition}',
                            '\App\Http\Controllers\Companies\CompanyLeasingConditionsController@deleteLeasingcondition');
                        Route::put(
                            '/{company}/leasing-conditions/{leasingCondition}',
                            '\App\Http\Controllers\Companies\CompanyLeasingConditionsController@editLeasingcondition');
                        Route::post('/{company}/insurance-rates',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@addInsuranceRate');
                        Route::post('/{company}/service-rates',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@addServiceRate');

                        Route::put('/{company}/insurance-rates/{insuranceRate}',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@editInsuranceRate');
                        Route::put('/{company}/service-rates/{serviceRate}',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@editServiceRate');

                        Route::delete('/{company}/insurance-rates/{insuranceRate}',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@deleteInsuranceRate');
                        Route::delete('/{company}/service-rates/{serviceRate}',
                            '\App\Http\Controllers\Companies\CompanyLeasingRatesController@deleteServiceRate');

                    });

                    Route::group(['prefix' => 'employees'], function () {
                        Route::get('/', 'V1\TechnicalServiceController@employees');
                        Route::get('/homepage', 'V1\EmployeeController@getDefaultHomepage');
                        Route::put('/homepage', 'V1\EmployeeController@updateDefaultHomepage');
                        Route::get('/{employee}', 'V1\TechnicalServiceController@employee');
                    });

                    Route::group(['prefix' => 'suppliers'], function () {
                        Route::get('/', 'V1\SupplierController@index');
                        Route::get('/all', 'V1\SupplierController@all');
                        Route::get('/{supplier}', 'V1\SupplierController@view');
                        Route::post('/', 'V1\SupplierController@create');
                        Route::post('/import', 'V1\SupplierController@import');
                        Route::post('/{supplier}', 'V1\SupplierController@update');
                        Route::get('/{supplier}/orders', 'V1\SupplierController@orders');
                        Route::get('/{supplier}/offers', 'V1\SupplierController@offers');
                        Route::get('/{supplier}/technical-services', 'V1\SupplierController@technicalServices');
                        Route::get('/{supplier}/homepage', 'V1\SupplierController@getHomepage');
                        Route::put('/{supplier}/homepage', 'V1\SupplierController@updateHomepage');
                    });

                    Route::group(['prefix' => 'widgets'], function () {
                        Route::get('/', 'V1\WidgetController@index');
                        Route::post('/', 'V1\WidgetController@create');
                        Route::post('/positions', 'V1\WidgetController@updatePositions');
                        Route::get('/sources', 'V1\WidgetController@sources');
                        Route::group(['middleware' => 'can:access-user-widget,widget'], function () {
                            Route::get('/{widget}', 'V1\WidgetController@view');
                            Route::post('/{widget}', 'V1\WidgetController@update');
                            Route::delete('/{widget}', 'V1\WidgetController@delete');
                        });
                    });

                    Route::group(['prefix' => 'notifications'], function () {
                        Route::group(['prefix' => 'senders'], function () {
                            Route::get('/', '\App\Http\Controllers\Notifications\NotificationController@senders');
                        });
                        Route::get('/', '\App\Http\Controllers\Notifications\NotificationController@index');
                        Route::get('/{notification}', '\App\Http\Controllers\Notifications\NotificationController@view');
                        Route::post('/', '\App\Http\Controllers\Notifications\NotificationController@create');
                    });

                    Route::group(['prefix' => 'cms'], function () {
                        Route::group(['prefix' => 'texts'], function () {
                            Route::get('/', '\App\Http\Controllers\Cms\TextController@index');
                            Route::put('/{text}', '\App\Http\Controllers\Cms\TextController@update');
                            Route::delete('/{text}', '\App\Http\Controllers\Cms\TextController@delete');
                        });
                    });

                    Route::group(['prefix' => 'offers'], function () {
                        Route::get('/statuses', 'V1\Base\OfferController@statuses');
                    });

                    Route::group(['prefix' => 'orders'], function () {
                        Route::get('/statuses', 'V1\Base\OrderController@statuses');
                    });

                    Route::group(['prefix' => '/portals', 'middleware' => []], function () {
                        Route::get('/{portal}', '\App\Http\Controllers\Portals\PortalController@view');
                        Route::put('/{portal}', '\App\Http\Controllers\Portals\PortalController@update');
                        Route::post(
                            '/{portal}/files',
                            '\App\Http\Controllers\Portals\PortalController@uploadFiles'
                        );
                        Route::post(
                            '/{portal}/insurance-rates',
                            '\App\Http\Controllers\Portals\PortalController@addInsuranceRate'
                        );
                        Route::post('/{portal}/service-rates', '\App\Http\Controllers\Portals\PortalController@addServiceRate');
                        Route::post(
                            '/{portal}/leasing-conditions',
                            '\App\Http\Controllers\Portals\PortalController@addLeasingCondition'
                        );
                        Route::put(
                            '/{portal}/insurance-rates/{insuranceRate}',
                            '\App\Http\Controllers\Portals\PortalController@editInsuranceRate'
                        );
                        Route::put(
                            '/{portal}/service-rates/{serviceRate}',
                            '\App\Http\Controllers\Portals\PortalController@editServiceRate'
                        );
                        Route::put(
                            '/{portal}/leasing-conditions/{leasingCondition}',
                            '\App\Http\Controllers\Portals\PortalController@editLeasingCondition'
                        );
                        Route::delete(
                            '/{portal}/service-rates/{serviceRate}',
                            '\App\Http\Controllers\Portals\PortalController@deleteServiceRate'
                        );
                    });

                    Route::group(['prefix' => 'leasing-settings'], function () {
                        Route::get('/', 'V1\DefaultLeasingSettingsController@index');
                    });

                    Route::group(['prefix' => 'product-categories'], function () {
                        Route::get('', '\App\Http\Controllers\Products\ProductCategoryController@list');
                    });

                    Route::group(['prefix' => 'geo'], function () {
                        Route::get('cities', 'V1\GeoController@cities');
                    });

                    Route::group(['prefix' => 'files'], function () {
                        Route::get('/', 'V1\FileController@download');
                    });

                    Route::group(['prefix' => 'technical-services'], function () {
                        Route::get('/employees/{employee}', 'V1\TechnicalServiceController@employeeTechnicalServices');
                        Route::get('/{technicalService}', 'V1\TechnicalServiceController@view');
                    });

                    Route::group(['prefix' => 'faqs'], function () {
                        Route::get('/', 'V1\FAQController@index');
                        Route::post('/', 'V1\FAQController@create');
                        Route::put('/{faq}', 'V1\FAQController@update');
                        Route::delete('/{faq}', 'V1\FAQController@delete');
                        Route::get('/categories', 'V1\FAQController@categories');
                        Route::post('/categories', 'V1\FAQController@createCategory');
                        Route::put('/categories/{category}', 'V1\FAQController@updateCategory');
                        Route::delete('/categories/{category}', 'V1\FAQController@deleteCategory');
                        Route::get('/categories/getOptions/{category}', 'V1\FAQController@getCategoryOptions');
                    });

                    Route::group(['prefix' => 'search'], function () {
                        Route::get('/', 'V1\SearchController@index');
                        Route::get('/live', 'V1\SearchController@live');
                    });

                    Route::group(['prefix' => 'homepage'], function () {
                        Route::get('/', 'V1\HomepageController@get');
                    });

                    Route::group(['prefix' => 'contracts'], function () {
                        Route::get('/', 'V1\ContractController@index');
                        Route::get('/search', 'V1\ContractController@search');
                        Route::get('/{contract}', 'V1\ContractController@view');
                        Route::post('/{contract}/cancel', 'V1\ContractController@cancel');
                    });

                });
            }
        );
    });
});
