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
        Route::group(['middleware' => 'guest:api', 'portal.company_slug:1'], function () {
            Route::post('login', 'Auth\LoginController@login');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/user-info', 'Auth\ResetPasswordController@userInfo');
            Route::post('refresh', 'Auth\LoginController@refresh');
        });

        Route::group([
            'middleware' => [
                'auth:api',
                'portal.jwt',
                'portal.company_slug:1',
                'portal.user_company_slug',
                'portal.user',
                'role:' . Role::ROLE_EMPLOYEE
            ]
        ], function () {
            Route::post('logout', 'Auth\LoginController@logout');

            Route::group(['prefix' => 'profile'], function () {
                Route::get('/', 'V1\ProfileController@view');
                Route::post('/', 'V1\ProfileController@update');
                Route::put('/', 'V1\ProfileController@updatePolicy');
            });

            Route::group(['prefix' => 'settings'], function () {
                Route::get('/', 'V1\Company\SettingController@index');
                Route::post('/download-leasingable-pdf', 'V1\SettingController@downloadLeasingablePdf');
            });

            Route::group(['prefix' => 'shops'], function () {
                Route::get('/', 'V1\Employee\SupplierController@index');
            });

            Route::group(['prefix' => 'offers'], function () {
                Route::get('/', 'V1\Employee\OfferController@index');
                Route::get('/statuses', 'V1\Employee\OfferController@statuses');
                Route::post('/', '\App\Http\Controllers\Offers\OfferController@create');
                Route::group(['middleware' => 'can:access-employee-offer,offer'], function () {
                    Route::get('/{offer}', '\App\Http\Controllers\Offers\OfferController@view');
                    Route::get('/{offer}/pdf', '\App\Http\Controllers\Offers\OfferController@downloadPdf');
                    Route::post('/{offer}', '\App\Http\Controllers\Offers\OfferController@edit');
                    Route::post('/{offer}/accept', 'V1\Employee\OfferController@accept');
                    Route::post('/{offer}/reject', 'V1\Employee\OfferController@reject');
                    Route::put('/{offer}/rates', 'V1\Employee\OfferController@changeRates');
                    Route::get('/{offer}/contract-data', 'V1\Employee\OfferController@getContractData');
                    Route::post('/{offer}/generate-contract-pdf', 'V1\Employee\OfferController@generateContractPdf');
                    Route::post('/{offer}/generate-contract', 'V1\Employee\OfferController@generateContract');
                });
            });

            Route::group(['prefix' => 'orders'], function () {
                Route::get('/', 'V1\Employee\OrderController@index');
                Route::get('/statuses', 'V1\Employee\OrderController@statuses');
                Route::group(['middleware' => 'can:access-employee-order,order'], function () {
                    Route::get('/{order}', 'V1\Employee\OrderController@view');
                    Route::post('/{order}/generate-offer-certificate-pdf', 'V1\Employee\OrderController@generateOfferCertificatePdf');
                    Route::post('/{order}/generate-lease-agreement-pdf', 'V1\Employee\OrderController@generateLeaseAgreementPdf');
                });
            });

            Route::group(['prefix' => 'technical-services'], function () {
                Route::get('/', 'V1\Employee\TechnicalServiceController@index');
                Route::get('/contracts-with-budget', 'V1\Employee\TechnicalServiceController@contractsWithBudget');
                Route::get('/statuses', 'V1\Employee\TechnicalServiceController@statuses');
                Route::get('/{technicalService}', 'V1\Employee\TechnicalServiceController@view');
            });

            Route::group(['prefix' => 'contracts'], function () {
                Route::get('/', 'V1\Employee\ContractController@index');
                Route::get('/statuses', 'V1\Employee\ContractController@statuses');
                Route::group(['middleware' => 'can:access-employee-contract,contract'], function () {
                    Route::post('/{contract}/technical-services', 'V1\Employee\TechnicalServiceController@createFromContract');
                    Route::get('/{contract}', 'V1\Employee\ContractController@view');
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

            Route::group(['prefix' => 'product-categories'], function () {
                Route::get('', 'V1\ProductCategoryController@allCompany');
            });

            Route::group(['prefix' => 'product-attributes'], function () {
                Route::get('sizes', 'V1\ProductAttributeController@getSizes');
                Route::get('colors', 'V1\ProductAttributeController@getColors');
            });

            Route::group(['prefix' => 'search'], function () {
                Route::get('/', 'V1\Employee\SearchController@index');
                Route::get('/live', 'V1\Employee\SearchController@live');
            });


            Route::group(['prefix' => 'homepage'], function () {
                Route::get('/', 'V1\Employee\HomepageController@get');
            });
        });

        Route::group(['prefix' => 'files', 'middleware' => 'can:access-employee-file,file'], function () {
            Route::get('/', 'V1\FileController@download');
        });

        Route::group(['prefix' => 'faqs'], function () {
            Route::get('/', 'V1\Employee\FAQController@index');
            Route::get('/categories', 'V1\Employee\FAQController@categories');
        });

        Route::group(['prefix' => 'geo'], function () {
            Route::get('cities', 'V1\GeoController@cities');
        });

        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/', '\App\Http\Controllers\Suppliers\SupplierController@list');
        });

        Route::group(['prefix' => 'companies'], function () {
            Route::get('/slug-exists', 'V1\CompanyController@slugExists');
        });
    });
});

Route::group([
    'prefix' => 'v1/offers',
    'middleware' => [
        'auth:api', 'can:access-employee-offer,offer',
    ]], function () {
    Route::get('/{offer}/generate-contract-pdf', 'V1\Employee\OfferController@generateContractPdf');
});
