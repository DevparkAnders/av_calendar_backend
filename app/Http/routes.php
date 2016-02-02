<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/
Route::group(['middleware' => 'api'], function () {
    Route::group(['middleware' => ['throttle:5,1', 'guest']], function () {
        Route::post('auth', 'AuthController@login');

        Route::post('password/reset', 'PasswordController@sendResetLinkEmail');
        Route::put('password/reset', 'PasswordController@reset');
    });

    Route::group(['middleware' => ['throttle:60,1', 'auth']], function () {
        Route::delete('auth', 'AuthController@logout');

        Route::group(['middleware' => 'refresh.token'], function () {
            Route::get('/', function () {
                // @todo this is test route only - will be removed later
                return response()->api(['item' => auth()->user()]);
            });
        });
    });
});
