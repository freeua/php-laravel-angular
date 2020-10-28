<?php
Route::group(['middleware' => ['portal.verify_domain']], function () {
    Route::get('portal-info', 'PortalController@getCurrent');
});

