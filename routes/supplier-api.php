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
        Route::group(['middleware' => 'guest:api'], function () {
            Route::get('portal-settings', 'V1\SettingController@index');
            Route::post('login', 'Auth\LoginController@login');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/user-info', 'Auth\ResetPasswordController@userInfo');
            Route::post('refresh', 'Auth\LoginController@refresh');
        });
        Route::group(['middleware' => ['auth:api', 'portal.jwt', 'portal.user', 'role:' . Role::ROLE_SUPPLIER_ADMIN]], function () {
            Route::post('logout', 'Auth\LoginController@logout');

            Route::group(['prefix' => 'profile'], function () {
                Route::get('/', 'V1\ProfileController@view');
                Route::post('/', 'V1\ProfileController@update');
            });

            Route::group(['prefix' => 'portals'], function () {
                Route::get('/{portal}', '\App\Http\Controllers\Portals\PortalController@view');
            });

            Route::group(['middleware' => 'pwd_age'], function () {
                Route::group(['prefix' => 'users'], function () {
                    Route::get('/', 'V1\Supplier\UserController@index');
                    Route::get('/search', 'V1\Supplier\UserController@search');
                    Route::post('/', 'V1\Supplier\UserController@create');
                    Route::group(['middleware' => 'can:access-supplier-user,user'], function () {
                        Route::post('/{user}', 'V1\Supplier\UserController@update');
                        Route::get('/{user}', 'V1\Supplier\UserController@view');
                    });
                });

                Route::group(['prefix' => 'offers'], function () {
                    Route::get('/', 'V1\Supplier\OfferController@index');
                    Route::post('/', '\App\Http\Controllers\Offers\OfferController@create');
                    Route::get('/statuses', 'V1\Supplier\OfferController@statuses');
                    Route::get('/units', 'V1\Supplier\OfferController@getUnits');
                    Route::get('/accessories', 'V1\Supplier\OfferController@getLastAccessories');
                    Route::group(['middleware' => 'can:access-supplier-offer,offer'], function () {
                        Route::get('/{offer}', 'V1\Supplier\OfferController@view');
                    });
                });

                Route::group(['prefix' => 'orders'], function () {
                    Route::get('/', 'V1\Supplier\OrderController@index');
                    Route::get('/statuses', 'V1\Supplier\OrderController@statuses');
                    Route::group(['middleware' => 'can:access-supplier-order,order'], function () {
                        Route::post('/{order}/ready', 'V1\Supplier\OrderController@ready');
                        Route::post('/{order}/pickup', 'V1\Supplier\OrderController@pickup');
                        Route::post('/{order}/upload-invoice', 'V1\Supplier\OrderController@uploadInvoice');
                        Route::get('/{order}/download-invoice', 'V1\Supplier\OrderController@downloadInvoice');
                        Route::get('/{order}', 'V1\Supplier\OrderController@view');
                    });
                });

                Route::group(['prefix' => 'technical-services'], function () {
                    Route::get('/inspections', 'V1\Supplier\TechnicalServiceController@inspections');
                    Route::get('/services', 'V1\Supplier\TechnicalServiceController@services');
                    Route::get('/statuses', 'V1\Supplier\TechnicalServiceController@statuses');
                    Route::get('/{technicalService}', 'V1\Supplier\TechnicalServiceController@view');
                    Route::put('/{technicalService}/accept', 'V1\Supplier\TechnicalServiceController@accept');
                    Route::put('/{technicalService}/ready', 'V1\Supplier\TechnicalServiceController@ready');
                    Route::put('/{technicalService}/complete', 'V1\Supplier\TechnicalServiceController@complete');
                    Route::post('/{technicalService}/service-pdf', 'V1\Supplier\TechnicalServiceController@generateServicePdf');
                });

                Route::group(['prefix' => 'companies'], function () {
                    Route::get('/', 'V1\Supplier\SupplierController@companies');
                });

                Route::group(['prefix' => 'notifications'], function () {
                    Route::group(['prefix' => 'senders'], function () {
                        Route::get('/', '\App\Http\Controllers\Notifications\NotificationController@senders');
                    });
                    Route::get('/', '\App\Http\Controllers\Notifications\NotificationController@index');
                    Route::get('/{notification}', '\App\Http\Controllers\Notifications\NotificationController@view');
                    Route::post('/', '\App\Http\Controllers\Notifications\NotificationController@create');
                });

                Route::group(['prefix' => 'product-categories'], function () {
                    Route::get('', 'V1\ProductCategoryController@all');
                    Route::get('/{company}', 'V1\ProductCategoryController@allForSupplier');
                });

                Route::group(['prefix' => 'product-brands'], function () {
                    Route::get('all', 'V1\ProductBrandController@all');
                });

                Route::group(['prefix' => 'product-models'], function () {
                    Route::get('all', 'V1\ProductModelController@all');
                });

                Route::group(['prefix' => 'product-attributes'], function () {
                    Route::get('sizes', 'V1\ProductAttributeController@getSizes');
                    Route::get('colors', 'V1\ProductAttributeController@getColors');
                });

                Route::group(['prefix' => 'widgets'], function () {
                    Route::get('/', 'V1\Supplier\WidgetController@index');
                    Route::post('/', 'V1\Supplier\WidgetController@create');
                    Route::post('/positions', 'V1\Supplier\WidgetController@updatePositions');
                    Route::get('/sources', 'V1\Supplier\WidgetController@sources');
                    Route::group(['middleware' => 'can:access-user-widget,widget'], function () {
                        Route::get('/{widget}', 'V1\Supplier\WidgetController@view');
                        Route::post('/{widget}', 'V1\Supplier\WidgetController@update');
                        Route::delete('/{widget}', 'V1\Supplier\WidgetController@delete');
                    });
                });

                Route::group(['prefix' => 'settings'], function () {
                    Route::get('/', 'V1\Supplier\SettingController@index');
                    Route::post('/', 'V1\Supplier\SettingController@update');
                    Route::post('/download-leasingable-pdf', 'V1\SettingController@downloadLeasingablePdf');
                });

                Route::group(['prefix' => 'files', 'middleware' => 'can:access-supplier-file,file'], function () {
                    Route::get('/', 'V1\FileController@download');
                });

                Route::group(['prefix' => 'geo'], function () {
                    Route::get('cities', 'V1\GeoController@cities');
                });

                Route::group(['prefix' => 'search'], function () {
                    Route::get('/', 'V1\Supplier\SearchController@index');
                    Route::get('/live', 'V1\Supplier\SearchController@live');
                });

                Route::group(['prefix' => 'homepage'], function () {
                    Route::get('/', 'V1\Supplier\HomepageController@get');
                });
            });
        });
    });
});

Route::group([
    'prefix' => 'v1/technical-services',
    'middleware' => [
        'auth:api', 'can:access-technical-service,technicalService',
    ]], function () {
    Route::get('/{technicalService}/service-pdf', 'V1\Supplier\TechnicalServiceController@generateServicePdf');
});

