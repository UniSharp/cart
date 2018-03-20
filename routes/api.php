<?php
Route::group(['prefix' => 'v1', 'namespace' => 'Api\\V1'], function () {
    Route::group(['prefix' => 'carts'], function () {
        Route::post('/', 'CartsController@store');
        Route::get('/{cart}', 'CartsController@show');
    });
});
