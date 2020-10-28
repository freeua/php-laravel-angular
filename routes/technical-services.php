<?php

Route::group(['middleware' => ['portal.verify_domain', 'auth:api']], function () {
    Route::group(['prefix' => 'technical-services'], function () {
        Route::get('/', 'TechnicalServiceController@index');
        Route::get('/full-service-contracts', 'TechnicalServiceController@fullServiceContracts');
        Route::get('/statuses', 'TechnicalServiceController@statuses');
        Route::post('/service-pdf', 'TechnicalServiceController@generateServicePdf');
        Route::get('/{technicalService}', 'TechnicalServiceController@view');
        Route::put('/{technicalService}/accept', 'TechnicalServiceController@accept');
        Route::put('/{technicalService}/ready', 'TechnicalServiceController@ready');
        Route::put('/{technicalService}/complete', 'TechnicalServiceController@complete');
    });

    Route::group(['prefix' => 'leasings/contracts', 'middleware' => 'can:access-employee-contract,contract'], function () {
            Route::post('/{contract}/technical-services', 'TechnicalServiceController@createFromContract');
    });
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::get('/technical-service-pdf', 'TechnicalServiceController@generateServicePdf');
});

Route::group(['middleware' => ['auth:system-api'], 'prefix' => 'system/technical-services'], function () {
    Route::get('/', 'TechnicalServiceController@index');
    Route::post('/service-pdf', 'TechnicalServiceController@generateServicePdf');
    Route::get('/statuses', 'TechnicalServiceController@statuses');
    Route::get('/{technicalService}', 'TechnicalServiceController@view');
});
