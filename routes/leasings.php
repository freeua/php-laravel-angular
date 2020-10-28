<?php
Route::group(['middleware' => ['oauth:leasings-partner']], function() {
    Route::post('users/{user}/accessories-reminder', 'UserController@sendAccessoriesReminder');

    Route::get('offers', 'OfferController@list');
    Route::get('offers/{offer}', 'OfferController@get');
    Route::post('offers', 'OfferController@create');

    Route::get('orders', 'OrderController@list');
    Route::get('orders/{order}', 'OrderController@get');
    Route::get('orders/{order}/credit-note', 'OrderController@downloadCreditNote');
    Route::put('orders/{order}/ready', 'OrderController@markAsReady');
    Route::put('orders/{order}/pick-up', 'OrderController@pickUp');
    Route::put('orders/{order}/read-credit-note', 'OrderController@markCreditNoteRead');

    Route::get('product-categories', 'ProductCategoryController@list');
});
