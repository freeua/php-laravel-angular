<?php
Route::group(['middleware' => ['partner_jwt']], function () {
    Route::get('userinfo', 'PartnerController@getUserInfo');
    Route::get('{partnerId}/verify-token', 'PartnerController@verifyToken');
});

Route::group(['middleware' => ['portal.verify_domain', 'auth:api']], function () {
    Route::get('{partnerId}/token', 'PartnerController@generateToken');
    Route::get('{partner}', 'PartnerController@getPartnerInfo');
});


Route::group(['middleware' => ['oauth:partners']], function () {
    Route::get('verify-token', 'PartnerController@verifyToken');
});

Route::group([], function () {
    Route::get('.well-known/jwks.json', 'PartnerController@getJwks');
});

