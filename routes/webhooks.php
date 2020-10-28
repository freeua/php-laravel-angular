<?php
Route::group(['middleware' => ['mailgun-webhook'], 'prefix' => 'webhooks'], function () {
    Route::post('all-mail', 'EmailController@handleAllMail');
});
