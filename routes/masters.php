<?php
Route::group(['middleware' => ['oauth:openid']], function() {
    Route::get('product-categories', 'OfferController@listProductCategories');
});