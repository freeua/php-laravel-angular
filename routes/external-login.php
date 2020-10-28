<?php
Route::group([], function () {
    Route::get('login/verify-challenge', 'ExternalLoginController@verifyLoginChallenge');
    Route::post('login', 'ExternalLoginController@login');
    Route::get('consent/verify-challenge', 'ExternalLoginController@verifyConsentChallenge');
    Route::put('consent/{challenge}/accept', 'ExternalLoginController@acceptConsent');
    Route::put('consent/{challenge}/reject', 'ExternalLoginController@rejectConsent');
});