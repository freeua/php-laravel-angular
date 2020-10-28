<?php

/*
|--------------------------------------------------------------------------
| System API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => 'guest:system-api'], function () {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');
    Route::get('password/user-info', 'Auth\ResetPasswordController@userInfo');
    Route::post('refresh', 'Auth\LoginController@refresh');
    Route::group(['prefix' => 'settings'], function () {
            Route::get('/', 'SettingController@index');
            Route::post('/', 'SettingController@update');
        });
});

Route::group(['middleware' => ['auth:system-api', 'system.jwt']], function () {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', 'ProfileController@view');
        Route::post('/', 'ProfileController@update');
    });

    Route::group(['middleware' => 'pwd_age'], function () {
        Route::group(['prefix' => 'portals'], function () {
            Route::get('/', '\App\Http\Controllers\Portals\PortalController@index');
            Route::post('/', '\App\Http\Controllers\Portals\PortalController@create');
            Route::get('/all', '\App\Http\Controllers\Portals\PortalController@all');
            Route::get('/{portal}', '\App\Http\Controllers\Portals\PortalController@view');
            Route::put('/{portal}', '\App\Http\Controllers\Portals\PortalController@update');
            Route::get('/{portal}/homepage', '\App\Http\Controllers\Portals\PortalController@getHomepage');
            Route::put('/{portal}/homepage', '\App\Http\Controllers\Portals\PortalController@updateHomepage');
            Route::post(
                '/{portal}/files',
                '\App\Http\Controllers\Portals\PortalController@uploadFiles'
            );

            Route::post('/{portal}/insurance-rates', '\App\Http\Controllers\Portals\PortalController@addInsuranceRate');
            Route::post('/{portal}/service-rates', '\App\Http\Controllers\Portals\PortalController@addServiceRate');
            Route::post('/{portal}/leasing-conditions', '\App\Http\Controllers\Portals\PortalController@addLeasingCondition');
            Route::put('/{portal}/insurance-rates/{insuranceRate}', '\App\Http\Controllers\Portals\PortalController@editInsuranceRate');
            Route::put('/{portal}/service-rates/{serviceRate}', '\App\Http\Controllers\Portals\PortalController@editServiceRate');
            Route::put('/{portal}/leasing-conditions/{leasingCondition}', '\App\Http\Controllers\Portals\PortalController@editLeasingCondition');
            Route::delete('/{portal}/insurance-rates/{insuranceRate}', '\App\Http\Controllers\Portals\PortalController@deleteInsuranceRate');
            Route::delete('/{portal}/service-rates/{serviceRate}', '\App\Http\Controllers\Portals\PortalController@deleteServiceRate');
            Route::delete('/{portal}/leasing-conditions/{leasingCondition}', '\App\Http\Controllers\Portals\PortalController@deleteLeasingCondition');
        });

        Route::group(['prefix' => 'users'], function () {
            Route::get('/', 'UserController@index');
            Route::get('/export', 'UserController@export');
            Route::post('/', 'UserController@create');
            Route::get('/{user}', 'UserController@view');
            Route::post('/{user}', 'UserController@update');
            Route::delete('/{user}', 'UserController@delete');
        });

        Route::group(['prefix' => 'portal-users'], function () {
            Route::post('/', 'PortalUserController@create');
            Route::post('/{user}', 'PortalUserController@update');
            Route::post('/{user}/login-as', 'PortalUserController@loginAs');
            Route::get('/{user}', 'PortalUserController@view');
            Route::delete('/{user}', 'PortalUserController@delete');
        });

        Route::group(['prefix' => 'suppliers'], function () {
            Route::get('/', 'SupplierController@index');
            Route::post('/', 'SupplierController@create');
            Route::get('/{supplier}', 'SupplierController@view');
            Route::post('/{supplier}', 'SupplierController@update');
            Route::post('/{supplier}/duplicate', 'SupplierController@duplicate');
        });

        Route::group(['prefix' => 'orders'], function () {
            Route::get('/', 'OrderController@index');
            Route::get('/statuses', 'OrderController@statuses');
            Route::get('/{order}/export', 'OrderController@export');
            Route::post('/{order}/convert', 'OrderController@convert');
            Route::get('/{order}/download-invoice', 'OrderController@downloadInvoice');
            Route::post('/{order}', 'OrderController@update');
            Route::get('/{id}', 'OrderController@view'); // Keep it the lowest in the list
        });

        Route::group(['prefix' => 'technical-services'], function () {
            Route::get('/', 'TechnicalServiceController@index');
            Route::get('/{technicalService}', 'TechnicalServiceController@view');
        });

        Route::group(['prefix' => 'contracts'], function () {
            Route::get('/', 'ContractController@index');
            Route::get('/export', 'ContractController@export');
            Route::get('/{contract}', 'ContractController@view');
            Route::get('/{contract}/export', 'ContractController@exportSingle');
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
            });
        });

        Route::group(['prefix' => 'geo'], function () {
            Route::get('cities', 'GeoController@cities');
        });

        Route::group(['prefix' => 'product-categories'], function () {
            Route::get('', '\App\Http\Controllers\Products\ProductCategoryController@list');
            Route::post('', '\App\Http\Controllers\Products\ProductCategoryController@create');
            Route::put('{productCategory}', '\App\Http\Controllers\Products\ProductCategoryController@edit');
            Route::delete('{productCategory}', '\App\Http\Controllers\Products\ProductCategoryController@delete');
            Route::post('/{productCategory}/insurance-rates', '\App\Http\Controllers\Products\ProductCategoryController@addInsuranceRate');
            Route::post('/{productCategory}/service-rates', '\App\Http\Controllers\Products\ProductCategoryController@addServiceRate');
            Route::post('/{productCategory}/leasing-conditions', '\App\Http\Controllers\Products\ProductCategoryController@addLeasingCondition');
            Route::put('/{productCategory}/insurance-rates/{insuranceRate}', '\App\Http\Controllers\Products\ProductCategoryController@editInsuranceRate');
            Route::put('/{productCategory}/service-rates/{serviceRate}', '\App\Http\Controllers\Products\ProductCategoryController@editServiceRate');
            Route::put('/{productCategory}/leasing-conditions/{leasingCondition}', '\App\Http\Controllers\Products\ProductCategoryController@editLeasingCondition');
            Route::delete('/{productCategory}/insurance-rates/{insuranceRate}', '\App\Http\Controllers\Products\ProductCategoryController@deleteInsuranceRate');
            Route::delete('/{productCategory}/service-rates/{serviceRate}', '\App\Http\Controllers\Products\ProductCategoryController@deleteServiceRate');
            Route::delete('/{productCategory}/leasing-conditions/{leasingCondition}', '\App\Http\Controllers\Products\ProductCategoryController@deleteLeasingCondition');
        });

        

        Route::group(['prefix' => 'feedback'], function () {
            Route::post('/', 'FeedbackController@create');
            Route::get('/categories', 'FeedbackController@categories');
        });

        Route::group(['prefix' => 'report'], function () {
            Route::post('/', 'ReportController@create');
            Route::get('/categories', 'ReportController@categories');
        });

        Route::group(['prefix' => 'files'], function () {
            Route::get('/', 'FileController@download');
        });

        Route::group(['prefix' => 'search'], function () {
            Route::get('/', 'SearchController@index');
            Route::get('/live', 'SearchController@live');
        });

        Route::group(['prefix' => 'widgets'], function () {
            Route::get('/', 'WidgetController@index');
            Route::post('/', 'WidgetController@create');
            Route::post('/positions', 'WidgetController@updatePositions');
            Route::get('/sources', 'WidgetController@sources');
            Route::get('/{widget}', 'WidgetController@view');
            Route::post('/{widget}', 'WidgetController@update');
            Route::delete('/{widget}', 'WidgetController@delete');
        });

        Route::group(['prefix' => 'faqs'], function () {
            Route::get('/', 'FAQController@index');
            Route::post('/', 'FAQController@create');
            Route::put('/{faq}', 'FAQController@update');
            Route::delete('/{faq}', 'FAQController@delete');
            Route::get('/categories', 'FAQController@categories');
            Route::post('/categories', 'FAQController@createCategory');
            Route::put('/categories/{category}', 'FAQController@updateCategory');
            Route::delete('/categories/{category}', 'FAQController@deleteCategory');
        });

    });
});

Route::group(['prefix' => 'export', 'middleware' => ['basicAuthMlf']], function() {
    Route::get('orders', '\App\Http\Controllers\Orders\ExportController@getOrders');
    Route::put('orders_transfered', '\App\Http\Controllers\Orders\ExportController@markAsTransferred');
    Route::group(['prefix' =>'orders/{order}/attachment'], function() {
        Route::get('uev_signed', '\App\Http\Controllers\Orders\ExportController@getSignedContract');
        Route::get('ueb', '\App\Http\Controllers\Orders\ExportController@getTakeoverDocument');
        Route::get('elv', '\App\Http\Controllers\Orders\ExportController@getSingleLeasingContract');
    });
});
