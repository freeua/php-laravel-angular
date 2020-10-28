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
        Route::group(['middleware' => 'guest:api', 'portal.company_slug'], function () {
            Route::post('login', 'Auth\LoginController@login');
            Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
            Route::post('password/reset', 'Auth\ResetPasswordController@reset');
            Route::get('password/user-info', 'Auth\ResetPasswordController@userInfo');
            Route::post('refresh', 'Auth\LoginController@refresh');

            Route::group(['prefix' => 'registration', 'middleware' => ['portal.company_slug:1']], function () {
                Route::post('/send-link', 'V1\Company\RegistrationController@sendLink');
                Route::post('/register', 'V1\Company\RegistrationController@register')->name('registration.register');
            });
        });

        Route::group([
            'prefix' => 'profile',
            'middleware' => [
                'auth:api',
                'portal.jwt',
                'portal.user',
                'portal.company_slug',
            ]
        ], function () {
            Route::get('/', 'V1\ProfileController@view');
            Route::post('/', 'V1\ProfileController@update');
        });

        Route::group(['prefix' => 'companies'], function () {
            Route::get('/slug-exists', 'V1\CompanyController@slugExists');
        });

        Route::group(['prefix' => 'portals'], function () {
            Route::get('/{portal}', '\App\Http\Controllers\Portals\PortalController@view');
        });

        Route::group([
            'middleware' => [
                'auth:api',
                'portal.jwt',
                'portal.user',
                'portal.company_slug',
                'role:' . Role::ROLE_COMPANY_ADMIN
            ]
        ], function () {
            Route::group(['middleware' => ['portal.user_company_slug']], function () {
                Route::post('logout', 'Auth\LoginController@logout');

                Route::group(['prefix' => 'settings'], function () {
                    Route::get('/', 'V1\Company\SettingController@index');
                });

                Route::group(['middleware' => ['pwd_age', 'can:companies.read-company-data,user']], function () {
                    Route::group(['prefix' => 'users'], function () {
                        Route::get('/', 'V1\Company\UserController@index');
                        Route::get('/homepage', 'V1\Company\UserController@getHomepage');
                        Route::put('/homepage', 'V1\Company\UserController@updateHomepage');
                        Route::get('/', 'V1\Company\UserController@index');
                        Route::get('/{user}', 'V1\Company\UserController@view');
                        Route::put('/{user}', 'V1\Company\UserController@update');
                        Route::put('/{user}/permissions', 'V1\UserController@updatePermissions');
                        Route::group(['middleware' => 'can:companies.manage-users,user'], function () {
                            Route::post('/{user}/approve', 'V1\Company\UserController@approve');
                            Route::post('/{user}/reject', 'V1\Company\UserController@reject');
                            Route::delete('/{user}', 'V1\Company\UserController@delete');
                        });
                    });

                    Route::group(['prefix' => 'permissions'], function () {
                        Route::get('/', 'V1\CompanyController@listPermissions');
                    });

                    Route::group(['prefix' => 'groups'], function () {
                        Route::get('/{company}', 'V1\CompanyController@listGroups');
                        Route::put('/switch/{user}', 'V1\CompanyController@switchGroup');
                    });

                    Route::group(['prefix' => 'offers'], function () {
                        Route::get('/', 'V1\Company\OfferController@index');
                        Route::get('/statuses', 'V1\Company\OfferController@statuses');
                        Route::post('/export', 'V1\Company\OfferController@exportPDF');
                        Route::get('/user/{user}', 'V1\Company\OfferController@userOffers');
                        Route::get('/supplier/{supplier}', 'V1\Company\OfferController@supplierOffers');
                        Route::group(['middleware' => 'can:access-company-offer,offer'], function () {
                            Route::post('/approve/{offer}', 'V1\Company\OfferController@approve');
                            Route::post('/reject/{offer}', 'V1\Company\OfferController@reject');
                            Route::get('/download-signed-contract/{offer}', 'V1\Company\OfferController@downloadSignedContract');
                            Route::get('/{offer}/offer-pdf', 'V1\Company\OfferController@downloadOfferPdf');
                            Route::get('/{offer}', 'V1\Company\OfferController@view');
                        });
                    });

                    Route::group(['prefix' => 'orders'], function () {
                        Route::get('/', 'V1\Company\OrderController@index');
                        Route::get('/statuses', 'V1\Company\OrderController@statuses');
                        Route::post('/export', 'V1\Company\OrderController@exportPDF');
                        Route::group(['middleware' => 'can:access-company-order,order'], function () {
                            Route::get('/{order}', 'V1\Company\OrderController@view');
                        });
                    });

                    Route::group(['prefix' => 'technical-services'], function () {
                        Route::get('/', 'V1\Company\TechnicalServiceController@index');
                        Route::get('/{technicalService}', 'V1\Company\TechnicalServiceController@view');
                    });

                    Route::group(['prefix' => 'contracts'], function () {
                        Route::get('/', 'V1\Company\ContractController@index');
                        Route::get('/statuses', 'V1\Company\ContractController@statuses');
                        Route::post('/export', 'V1\Company\ContractController@export');
                        Route::group(['middleware' => 'can:access-company-contract,contract'], function () {
                            Route::get('/{contract}', 'V1\Company\ContractController@view');
                        });
                    });

                    Route::group(['prefix' => 'suppliers'], function () {
                        Route::get('/all', 'V1\SupplierController@all');
                        Route::get('/', 'V1\Company\CompanyController@suppliers');
                        Route::get('/list', 'V1\Company\SupplierController@list');
                        Route::get('/{supplier}', 'V1\Company\SupplierController@view');
                        Route::group([
                            'middleware' => 'can:companies.edit-company-data,user',
                        ], function () {
                            Route::post('/', 'V1\Company\CompanyController@storeSuppliers');
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

                    Route::group(['prefix' => 'widgets'], function () {
                        Route::get('/', 'V1\Company\WidgetController@index');
                        Route::post('/', 'V1\Company\WidgetController@create');
                        Route::post('/positions', 'V1\Company\WidgetController@updatePositions');
                        Route::get('/sources', 'V1\Company\WidgetController@sources');
                        Route::group(['middleware' => 'can:access-user-widget,widget'], function () {
                            Route::get('/{widget}', 'V1\Company\WidgetController@view');
                            Route::post('/{widget}', 'V1\Company\WidgetController@update');
                            Route::delete('/{widget}', 'V1\Company\WidgetController@delete');
                        });
                    });

                    Route::group(['prefix' => 'settings'], function () {
                        Route::get('/', 'V1\Company\SettingController@index');
                        Route::post('/download-leasingable-pdf', 'V1\SettingController@downloadLeasingablePdf');
                        Route::group([
                            'middleware' => 'can:companies.edit-company-data,user',
                        ], function () {
                            Route::post('/', 'V1\Company\SettingController@update');
                        });
                    });

                    Route::group(['prefix' => 'product-categories'], function () {
                        Route::get('all', 'V1\ProductCategoryController@allCompany');
                    });

                    Route::group(['prefix' => 'geo'], function () {
                        Route::get('cities', 'V1\GeoController@cities');
                    });

                    Route::group(['prefix' => 'search'], function () {
                        Route::get('/', 'V1\Company\SearchController@index');
                        Route::get('/live', 'V1\Company\SearchController@live');
                    });

                    Route::group(['prefix' => 'geo'], function () {
                        Route::get('cities', 'V1\GeoController@cities');
                    });

                    Route::group(['prefix' => 'faqs'], function () {
                        Route::get('/', 'V1\Company\FAQController@index');
                        Route::post('/', 'V1\Company\FAQController@create');
                        Route::put('/{faq}', 'V1\Company\FAQController@update');
                        Route::delete('/{faq}', 'V1\Company\FAQController@delete');
                        Route::get('/categories', 'V1\Company\FAQController@categories');
                        Route::post('/categories', 'V1\Company\FAQController@createCategory');
                        Route::put('/categories/{category}', 'V1\Company\FAQController@updateCategory');
                        Route::delete('/categories/{category}', 'V1\Company\FAQController@deleteCategory');
                        Route::get('/categories/getOptions/{category}', 'V1\Company\FAQController@getCategoryOptions');
                    });
                    Route::group(['prefix' => 'companies'], function () {
                        Route::get('/{company}', 'V1\CompanyController@view');
                        Route::put('/{company}', 'V1\CompanyController@updateByCompanyAdmin');
                        Route::get('/{company}/employees', 'V1\CompanyController@listEmployees');
                        Route::get('/{company}/admins', 'V1\CompanyController@listAdmins');
                    });

                    Route::group(['prefix' => 'homepage'], function () {
                        Route::get('/', 'V1\Company\HomepageController@get');
                    });
                });
            });
        });

        Route::group(['prefix' => 'files', 'middleware' => 'can:access-company-file,file'], function () {
            Route::get('/', 'V1\FileController@download');
        });
    });
});
