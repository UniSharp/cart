<?php
Route::group(['prefix' => 'v1', 'namespace' => 'Api\\V1'], function () {
    Route::group(['prefix' => 'carts'], function () {
        Route::post('/', 'CartsController@store');
        Route::post('/{cart}', 'CartsController@refresh');
        Route::put('/{cart}', 'CartsController@update');
        Route::get('/{cart}', 'CartsController@show');
        Route::delete('/{cart}/{item}', 'CartsController@delete');
        Route::delete('/{cart}/', 'CartsController@destroy');
    });
    Route::group(['prefix' => 'orders', 'middleware' => config('cart.middlewares', [])], function () {
        Route::post('/', 'OrdersController@store');
        Route::put('/{order}', 'OrdersController@update');
        Route::get('/{order}', 'OrdersController@show');
        Route::delete('/{order}/{item}', 'OrdersController@delete');
        Route::delete('/{order}/', 'OrdersController@destroy');
    });
});
