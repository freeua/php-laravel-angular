<?php

Route::group(['middleware' => ['portal.verify_domain', 'auth:api']], function () {
    Route::get('documents', 'DocumentsController@index');
    Route::post('documents', 'DocumentsController@upload');
    Route::get('documents/{document}/download', 'DocumentsController@download');
    Route::put('documents/{document}/toggle-visibility', 'DocumentsController@toggleVisibility');
    Route::delete('documents/{document}', 'DocumentsController@delete');
});
Route::group(['middleware' => ['auth:api']], function () {
    Route::get('documents/{document}/download', 'DocumentsController@download');
    Route::get('documents/{document}/show-pdf', 'DocumentsController@showPdf');
});

Route::group(['middleware' => ['auth:system-api'], 'prefix' => 'system'], function () {
    Route::get('documents', 'DocumentsController@index');
    Route::post('documents', 'DocumentsController@upload');
    Route::get('documents/{document}/download', 'DocumentsController@download');
    Route::get('documents/{document}/show-pdf', 'DocumentsController@showPdf');
    Route::put('documents/{document}/toggle-visibility', 'DocumentsController@toggleVisibility');
    Route::delete('documents/{document}', 'DocumentsController@delete');
});
